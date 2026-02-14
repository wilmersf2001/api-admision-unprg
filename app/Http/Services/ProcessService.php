<?php

namespace App\Http\Services;

use App\Models\Process;
use Exception;
use Illuminate\Http\Request;

class ProcessService
{
    protected Process $model;
    private string $nameModel = 'Proceso';

    public function __construct(Process $model)
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
            if (Process::existActiveProcess())
            {
                throw new Exception('Ya existe un proceso activo. No se puede crear otro proceso hasta que el actual sea desactivado.');
            }
            return $this->model->create($data);
        }catch (\Throwable $th){
            throw new Exception($th->getMessage());
        }
    }

    public function update($id, array $data)
    {
        $record = $this->model->find($id);
        if (!$record) {
            throw new Exception($this->nameModel . ' no encontrado');
        }
        try {
            if (Process::existActiveProcess())
            {
                if (isset($data['estado']) && $data['estado'] == 1 && $record->estado == 0) {
                    throw new Exception('Ya existe un proceso activo. No se puede activar otro proceso hasta que el actual sea desactivado.');
                }
            }
            $record->update($data);
            return $record;
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
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
