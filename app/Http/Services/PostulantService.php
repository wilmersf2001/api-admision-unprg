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
    protected FileService $fileService;
    protected Postulant $model;
    protected BankService $bankService;
    private string $nameModel = 'Postulante';

    public function __construct(Postulant $model, BankService $bankService, FileService $fileService)
    {
        $this->model = $model;
        $this->bankService = $bankService;
        $this->fileService = $fileService;
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
            $data['codigo'] = substr((string) $data['num_documento'], 0, 8);
            $data['ingreso'] = 0;
            $data['estado_postulante_id'] = $data['estado_postulante_id'] ?? 1; // Estado inicial
            $data['pais_id'] = $data['pais_id'] ?? Constants::ID_PERU; // Perú por defecto
            $imagePostulanteFile = $data['foto_postulante'] ?? null;
            $imageDniAnversoFile = $data['dni_anverso'] ?? null;
            $imageDniReversoFile = $data['dni_reverso'] ?? null;
            unset($data['foto_postulante'], $data['dni_anverso'], $data['dni_reverso']); // No existen en la tabla postulantes, se guardarán como archivos relacionados

            // Crear el postulante
            $postulant = $this->model->create($data);

            // Marcar el pago como usado
            $this->bankService->markAsUsed($bank, $postulant->id);

            // Adjuntar archivos si existen
            if ($imagePostulanteFile) {
                $this->attachFile($postulant, $imagePostulanteFile, 'image', 'foto_postulante');
            }
            if ($imageDniAnversoFile) {
                $this->attachFile($postulant, $imageDniAnversoFile, 'image', 'foto_dni_anverso');
            }
            if ($imageDniReversoFile) {
                $this->attachFile($postulant, $imageDniReversoFile, 'image', 'foto_dni_reverso');
            }

            return $postulant;
        });
    }

    private function attachFile(Postulant $postulant, $uploadedFile, string $type, string $typeEntitie): void
    {
        // Subir archivo usando FileService
        $file = $this->fileService->upload(
            $postulant,
            $uploadedFile,
            true, // isPublic
            $type,
            $typeEntitie,
            'postulant_files' // directorio
        );
    }
}
