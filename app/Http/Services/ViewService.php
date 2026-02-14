<?php

namespace App\Http\Services;

use App\Models\View;
use Exception;
use Illuminate\Http\Request;

class ViewService
{
    protected View $model;
    private string $nameModel = 'Vista';

    public function __construct(View $model)
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

        if (isset($data['parent_id']) && $data['parent_id'] === $record->id) {
            throw new Exception('Una vista no puede ser su propio padre');
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
        try {
            $record->delete();
            return true;
        } catch (\Throwable $th) {
            throw new Exception('Error al eliminar ' . $this->nameModel . ': ' . $th->getMessage());
        }
    }
}
