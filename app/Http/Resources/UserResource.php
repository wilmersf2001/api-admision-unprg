<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'role_id' => $this->role_id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->when($this->relationLoaded('role'), function () {
                return [
                    'id' => $this->role->id,
                    'name' => $this->role->name,
                    'description' => $this->role->description,
                ];
            }),
            'views' => $this->when($this->relationLoaded('role'), function () {
                return $this->getViewsWithPermissions();
            }),
            'status' => $this->status,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Obtiene las vistas con sus permisos otorgados
     */
    private function getViewsWithPermissions(): array
    {
        if (!$this->role || !$this->role->relationLoaded('rolePermission')) {
            return [];
        }

        // Agrupar permisos por vista
        $viewsPermissions = [];

        foreach ($this->role->rolePermission as $rolePermission) {
            if (!$rolePermission->permission || !$rolePermission->permission->view) {
                continue;
            }

            $permission = $rolePermission->permission;
            $view = $permission->view;

            if (!isset($viewsPermissions[$view->id])) {
                $viewsPermissions[$view->id] = [
                    'id' => $view->id,
                    'name' => $view->name,
                    'slug' => $view->slug,
                    'route' => $view->route,
                    'icon' => $view->icon,
                    'description' => $view->description,
                    'order' => $view->order,
                    'parent_id' => $view->parent_id,
                    'permissions' => [],
                ];
            }

            // Agregar permiso a la vista
            $viewsPermissions[$view->id]['permissions'][] = [
                'id' => $permission->id,
                'code' => $permission->code,
                'name' => $permission->name,
                'description' => $permission->description,
                'module' => $permission->module,
                'policy_method' => $permission->policy_method,
            ];
        }

        return array_values($viewsPermissions);
    }
}
