<?php
namespace App\Http\Services;

use App\Models\Department;
use App\Models\District;
use Illuminate\Http\Request;

class DistrictService
{
    protected District $model;

    public function __construct(District $model)
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
}
