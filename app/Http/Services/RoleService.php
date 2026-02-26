<?php

namespace App\Http\Services;

use App\Models\Role;
use Exception;
use Illuminate\Http\Request;

class RoleService
{
    protected Role $model;
    private string $nameModel = 'Rol';

    public function __construct(Role $model)
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

    public function create(array $data)
    {
        try {
            return $this->model->create($data);
        }catch (\Throwable $th){
            throw new Exception('Error al crear '. $this->nameModel. ' :' . $th->getMessage());
        }
    }

    public function update($id, array $data)
    {
        $record = $this->model->find($id);
        if (!$record) {
            throw new Exception($this->nameModel . ' no encontrado');
        }

        if (isset($data['is_active']) && !$data['is_active'] && $record->id === Role::ROLE_ADMIN_ID) {
            throw new Exception('No se puede desactivar el usuario administrador');
        }

        try {
            $record->update($data);
            return $record;
        } catch (\Throwable $th) {
            throw new Exception('Error al actualizar ' . $this->nameModel . ': ' . $th->getMessage());
        }
    }

    public function delete($id){
        $record = $this->model->find($id);
        if (!$record) {
            throw new Exception($this->nameModel . ' no encontrado');
        }

        if ($record->id === Role::ROLE_ADMIN_ID) {
            throw new Exception('No se puede eliminar el usuario administrador');
        }

        try {
            $record->delete();
            return true;
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }
}
