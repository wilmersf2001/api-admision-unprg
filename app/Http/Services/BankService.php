<?php

namespace App\Http\Services;

use App\Http\Utils\Constants;
use App\Models\Bank;
use Exception;
use Illuminate\Http\Request;

class BankService
{
    protected Bank $model;

    public function __construct(Bank $model)
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

    public function verifyPayment(array $data)
    {
        $exists = $this->model->where('num_doc_depo', $data['num_doc_depo'])
            ->where('num_documento', $data['num_documento'])
            ->where('num_oficina', $data['num_oficina'])
            ->whereDate('fecha', $data['fecha'])
            ->exists();

        if (!$exists) {
            throw new Exception($this->nameModel . ' no encontrado');
        }

        $record = $this->model->where('num_doc_depo', $data['num_doc_depo'])
            ->where('num_documento', $data['num_documento'])
            ->where('num_oficina', $data['num_oficina'])
            ->whereDate('fecha', $data['fecha'])
            ->first();

        if ($data['tipo_colegio'] === 'Nacional') {
            if ($record->importe !== Constants::MONTO_NACIONAL){
                throw new Exception('El monto del pago no coincide para colegio Nacional');
            }
        } elseif ($data['tipo_colegio'] === 'Particular') {
            if ($record->importe !== Constants::MONTO_PARTICULAR){
                throw new Exception('El monto del pago no coincide para colegio Particular');
            }
        }

        return $record;
    }
}

