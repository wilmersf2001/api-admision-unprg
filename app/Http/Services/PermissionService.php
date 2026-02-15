<?php

namespace App\Http\Services;

use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\View;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionService
{
    protected Permission $model;
    private string $nameModel = 'Permiso';

    public function __construct(Permission $model)
    {
        $this->model = $model;
    }

    public function getFiltered(Request $request)
    {
        $query = $this->model->newQuery(); // Inicia una nueva consulta
        $query->applyFilters($request); // Lee getFilterConfig() y filtra
        $query->applySort($request); // Lee getSortConfig() y ordena
        return $query->applyPagination($request); // all=true = paginado, all=false = sin paginar
    }

    public function bulkSync(array $data)
    {
        DB::beginTransaction();
        try {
            $module = $data['module'];
            $moduleName = $data['module_name'];
            $actions = $data['actions'];
            $vistaId = $data['view_id'] ?? null;
            $isActive = $data['is_active'] ?? true;

            if ($vistaId) {
                $view = View::find($vistaId);
                if ($view->submodule || $view->route === null) {
                    throw new \Exception("No se puede asignar la acción 'view' a una vista que es submódulo");
                }
            }

            $permissionsConfig = config('permissions.actions');
            $syncedPermissions = [];
            $createdCount = 0;
            $deletedCount = 0;

            // 1. Obtener todos los permisos existentes para este módulo (y view_id si existe)
            $existingPermissionsQuery = Permission::where('module', $module);

            if ($vistaId !== null) {
                $existingPermissionsQuery->where('view_id', $vistaId);
            } else {
                $existingPermissionsQuery->whereNull('view_id');
            }

            $existingPermissions = $existingPermissionsQuery->get();
            $existingCodes = $existingPermissions->pluck('code')->toArray();

            // 2. Crear/obtener/restaurar los permisos según las acciones enviadas
            $expectedCodes = [];
            foreach ($actions as $action) {
                // Validar que la acción existe en el config
                if (!isset($permissionsConfig[$action])) {
                    throw new \Exception("Acción '{$action}' no es válida");
                }

                $actionConfig = $permissionsConfig[$action];
                $code = "{$module}.{$action}";
                $expectedCodes[] = $code;

                // Verificar si ya existe el permiso (incluyendo soft-deleted)
                $existingPermission = Permission::withTrashed()->where('code', $code)->first();

                if ($existingPermission) {
                    // Si existe pero está eliminado (soft delete), restaurarlo
                    if ($existingPermission->trashed()) {
                        $existingPermission->restore();
                        $createdCount++; // Contar como "creado" porque fue restaurado
                    }

                    // SIEMPRE actualizar los datos (esté eliminado o activo)
                    $existingPermission->update([
                        'name' => "{$actionConfig['label']} {$moduleName}",
                        'description' => $actionConfig['description'],
                        'module' => $module,
                        'view_id' => $vistaId,
                        'policy_method' => $actionConfig['policy_method'] ?? $action,
                        'is_active' => $isActive,
                    ]);

                    // Agregar a la respuesta
                    $syncedPermissions[] = new PermissionResource($existingPermission->fresh());
                    continue;
                }

                // Crear el nuevo permiso (solo si no existe en absoluto)
                $permission = Permission::create([
                    'code' => $code,
                    'name' => "{$actionConfig['label']} {$moduleName}",
                    'description' => $actionConfig['description'],
                    'module' => $module,
                    'view_id' => $vistaId,
                    'policy_method' => $actionConfig['policy_method'] ?? $action,
                    'is_active' => $isActive,
                ]);

                $syncedPermissions[] = new PermissionResource($permission);
                $createdCount++;
            }

            // 3. Eliminar permisos que ya no están en la lista de acciones enviadas
            $codesToDelete = array_diff($existingCodes, $expectedCodes);

            if (!empty($codesToDelete)) {
                $deleted = Permission::whereIn('code', $codesToDelete)->delete();
                $deletedCount = $deleted;
            }

            DB::commit();

            // Retornar un objeto con la estructura esperada
            return (object) [
                'permissions' => $syncedPermissions,
                'created_count' => $createdCount,
                'deleted_count' => $deletedCount,
                'message' => "Sincronización completa: {$createdCount} creado(s), {$deletedCount} eliminado(s)"
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Error al sincronizar permisos: " . $e->getMessage());
        }
    }

    public function savePermissionsToRole(int $roleId, array $permissionIds): array
    {
        DB::beginTransaction();
        try {
            Role::findOrFail($roleId);

            // Procesar cada permiso individualmente
            foreach ($permissionIds as $permissionId) {
                // Verificar si ya existe la relación
                $exists = RolePermission::where('role_id', $roleId)
                    ->where('permission_id', $permissionId)
                    ->exists();

                if (!$exists) {
                    // Crear solo si no existe
                    RolePermission::create([
                        'role_id' => $roleId,
                        'permission_id' => $permissionId,
                        'granted' => true,
                    ]);
                } else {
                    // Actualizar si ya existe
                    RolePermission::where('role_id', $roleId)
                        ->where('permission_id', $permissionId)
                        ->update([
                            'granted' => true,
                            'updated_at' => now(),
                        ]);
                }
            }

            DB::commit();
            return ['message' => 'Permisos guardados correctamente'];
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Error al guardar permisos: " . $e->getMessage());
        }
    }

    public function removePermissionFromRole(int $roleId, int $permissionId): bool
    {
        DB::beginTransaction();
        try {
            // Obtener el permiso para verificar si es de tipo "view"
            Permission::find($permissionId);

            // Eliminar la relación role-permission
            RolePermission::where('role_id', $roleId)
                ->where('permission_id', $permissionId)
                ->delete();

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Error al remover permiso: " . $e->getMessage());
        }
    }
}
