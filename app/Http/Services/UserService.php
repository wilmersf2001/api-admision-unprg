<?php

namespace App\Http\Services;


use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class UserService
{
    protected User $model;
    private string $nameModel = 'Usuario';

    public function __construct(User $model)
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
        if (isset($data['status']) && !$data['status'] && $record->id === User::USER_ADMIN_ID) {
            throw new Exception('No se puede desactivar el usuario administrador');
        }

        if (isset($data['password']) && $record->id === User::USER_ADMIN_ID) {
            if (!$this->isStrongPassword($data['password'])) {
                throw new Exception('La contraseña del administrador debe tener al menos 8 caracteres, incluir mayúsculas, minúsculas, números y caracteres especiales');
            }
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
        if ($record->id === User::USER_ADMIN_ID) {
            throw new Exception('No se puede eliminar el usuario administrador');
        }
        try {
            $record->delete();
            return true;
        } catch (\Throwable $th) {
            throw new Exception('Error al eliminar ' . $this->nameModel . ': ' . $th->getMessage());
        }
    }

    private function isStrongPassword(string $password): bool
    {
        return strlen($password) >= 8
            && preg_match('/[a-z]/', $password)      // Al menos una minúscula
            && preg_match('/[A-Z]/', $password)      // Al menos una mayúscula
            && preg_match('/[0-9]/', $password)      // Al menos un número
            && preg_match('/[@$!%*?&#]/', $password); // Al menos un carácter especial
    }
}
