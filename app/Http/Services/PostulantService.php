<?php

namespace App\Http\Services;

use App\Http\Services\BankService;
use App\Http\Utils\Constants;
use App\Models\Postulant;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostulantService
{
    protected Postulant $model;
    protected BankService $bankService;
    private string $nameModel = 'Postulante';

    public function __construct(Postulant $model, BankService $bankService)
    {
        $this->model = $model;
        $this->bankService = $bankService;
    }

    public function getFiltered(Request $request)
    {
        $query = $this->model->newQuery();
        $query->applyFilters($request);
        $query->applySort($request);
        return $query->applyPagination($request);
    }

    public function update($id, array $data)
    {
        $record = $this->model->find($id);
        if (!$record) {
            throw new Exception($this->nameModel . ' no encontrado');
        }
        try {
            $record->update($data);
            return $record;
        } catch (\Throwable $th) {
            throw new Exception('Error al actualizar ' . $this->nameModel . ': ' . $th->getMessage());
        }
    }

    /**
     * Registra un postulante usando el token de inscripción
     * Usa transacción y bloqueo para evitar condiciones de carrera
     *
     * @param array $data Datos del postulante
     * @param string $token Token de inscripción
     * @return Postulant
     * @throws Exception
     */
    public function registerWithToken(array $data, string $token): Postulant
    {
        // Validar el token primero (fuera de la transacción)
        $payload = $this->bankService->validateInscriptionToken($token);

        // Usar transacción para garantizar atomicidad
        return DB::transaction(function () use ($data, $payload) {

            // Obtener el banco con bloqueo para evitar race conditions
            $bank = $this->bankService->getBankForRegistration($payload->bank_id);

            // Preparar datos del postulante
            $data['fecha_inscripcion'] = now();
            $data['codigo'] = $this->generateCode();
            $data['ingreso'] = 0;
            $data['estado_postulante_id'] = $data['estado_postulante_id'] ?? 1; // Estado inicial
            $data['pais_id'] = $data['pais_id'] ?? Constants::ID_PERU; // Perú por defecto

            // Crear el postulante
            $postulant = $this->model->create($data);

            // Marcar el pago como usado
            $this->bankService->markAsUsed($bank, $postulant->id);

            return $postulant;
        });
    }

    /**
     * Genera un código único para el postulante
     *
     * @return string
     */
    private function generateCode(): string
    {
        $year = date('Y');
        $lastPostulant = $this->model
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastPostulant && preg_match('/(\d+)$/', $lastPostulant->codigo, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return $year . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
