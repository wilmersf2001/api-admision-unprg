<?php

namespace App\Http\Services;

use App\Http\Utils\Constants;
use App\Http\Utils\UtilFunction;
use App\Models\Bank;
use App\Models\Content;
use App\Models\District;
use App\Models\File;
use App\Models\Postulant;
use App\Models\PostulantState;
use App\Models\Process;
use App\Models\School;
use App\Models\UpdateRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostulantService
{
    protected FileService $fileService;
    protected QrCodeService $qrCodeService;
    protected Postulant $model;
    protected BankService $bankService;
    private string $nameModel = 'Postulante';

    public function __construct(Postulant $model, BankService $bankService, FileService $fileService, QrCodeService $qrCodeService)
    {
        $this->model = $model;
        $this->bankService = $bankService;
        $this->fileService = $fileService;
        $this->qrCodeService = $qrCodeService;
    }

    public function getFiltered(Request $request)
    {
        $query = $this->model->newQuery()->with('files');
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
            $numDocumento = $data['num_documento'];
            if ($imagePostulanteFile) {
                $this->attachFile($postulant, $imagePostulanteFile, 'image', 'foto_postulante', Constants::RUTA_FOTO_CARNET_INSCRIPTO, $numDocumento);
            }
            if ($imageDniAnversoFile) {
                $this->attachFile($postulant, $imageDniAnversoFile, 'image', 'foto_dni_anverso', Constants::RUTA_DNI_ANVERSO_INSCRIPTO, 'A-' . $numDocumento);
            }
            if ($imageDniReversoFile) {
                $this->attachFile($postulant, $imageDniReversoFile, 'image', 'foto_dni_reverso', Constants::RUTA_DNI_REVERSO_INSCRIPTO, 'R-' . $numDocumento);
            }

            $this->qrCodeService->save($postulant);

            return $postulant;
        });
    }

    public function getFilePDF($id)
    {
        try {
            $postulant = Postulant::findOrFail($id);

            if (!in_array($postulant->estado_postulante_id, Constants::ESTADOS_VALIDOS_POSTULANTE_ADMISION)) {
                throw new Exception('No se puede generar el PDF para un postulante con estado válido o posterior.');
            }

            $today = UtilFunction::getDateToday();
            $pathImagePostulante =  Postulant::getImagePathByDni($postulant);
            $process = Process::getProcessNumber();
            $lugarNacimiento = $postulant->tipo_documento === Postulant::DOCUMENT_TYPE_DNI ? District::getLocationByDistrito($postulant->distrito_nac_id) : $postulant->country->nombre;
            $school = $postulant->school;

            if ($postulant->distrito_res_id == $postulant->distrito_nac_id) {
                $lugarResidencia = $lugarNacimiento;
            } else {
                $lugarResidencia = District::getLocationByDistrito($postulant->distrito_res_id);
            }

            if ($school->distrito_id == $postulant->distrito_nac_id) {
                $lugarColegio = $lugarNacimiento;
            } else {
                if ($school->distrito_id == $postulant->distrito_res_id) {
                    $lugarColegio = $lugarResidencia;
                } else {
                    $lugarColegio = District::getLocationByDistrito($school->distrito_id);
                }
            }

            $data = [
                'postulante' => $postulant,
                'resultadoQr' => $this->qrCodeService->generateData($postulant),
                'programaAcademico' => $postulant->academicProgram->nombre,
                'modalidad' => $postulant->modality->nombre,
                'sede' => $postulant->sede->nombre,
                'colegio' => $school->nombre,
                'lugarNacimiento' => UtilFunction::formatearLocalizacion($lugarNacimiento),
                'lugarResidencia' => UtilFunction::formatearLocalizacion($lugarResidencia),
                'lugarColegio' => UtilFunction::formatearLocalizacion($lugarColegio),
                'process' => $process,
                'today' => $today,
                'tipoColegio' => $school->tipo == School::TYPE_NATIONAL ? 'Nacional' : 'Privado',
                'laberBirth' => $postulant->tipo_documento == Postulant::DOCUMENT_TYPE_DNI ? 'Lugar de nacimiento' : 'País de procedencia',
                'base64ImagePostulante' => "data:image/png;base64," . base64_encode(file_get_contents(public_path($pathImagePostulante))),
                'base64ImageLogoUnprg' => "data:image/png;base64," . base64_encode(file_get_contents(public_path('images/logo_color.png'))),
                'swornDeclarationTitle' => Content::where('code', Content::SWORN_DECLARATION_FORMAT)->first()->title ?? 'DECLARACIÓN JURADA',
                'swornDeclarationContent' => Content::where('code', Content::SWORN_DECLARATION_FORMAT)->first()->content ?? [],
            ];

            return PDF::loadView('reports.pdf-postulante', $data)->stream();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getValidFiles(Request $request)
    {
        return $this->getPostulantsByFolder(
            Constants::CARPETA_ARCHIVOS_VALIDOS,
            PostulantState::INSCRITO_WEB,
            $request
        );
    }

    public function getObservedFiles(Request $request)
    {
        return $this->getPostulantsByFolder(
            Constants::CARPETA_ARCHIVOS_OBSERVADOS,
            PostulantState::INSCRITO_WEB,
            $request
        );
    }

    public function getObservedReiteratedFiles(Request $request)
    {
        return $this->getPostulantsByFolder(
            Constants::CARPETA_ARCHIVOS_RECTIFICADOS,
            PostulantState::ARCHIVOS_ENVIO_OBSERVADOS,
            $request,
            Constants::CARPETA_ARCHIVOS_VALIDOS
        );
    }

    public function getRectifiedFiles(Request $request)
    {
        return $this->getPostulantsByFolder(
            Constants::CARPETA_ARCHIVOS_VALIDOS,
            PostulantState::ARCHIVOS_ENVIO_OBSERVADOS,
            $request
        );
    }

    private function getPostulantsByFolder(string $folder, string $estadoId, Request $request, ?string $excludeFolder = null)
    {
        $disk = Constants::DISK_STORAGE;

        $dnisFotoCarnet = $this->extractDnisFromFolder($disk, $folder . Constants::CARPETA_FOTO_CARNET, '');
        $dnisDniAnverso = $this->extractDnisFromFolder($disk, $folder . Constants::CARPETA_DNI_ANVERSO, 'A-');
        $dnisDniReverso = $this->extractDnisFromFolder($disk, $folder . Constants::CARPETA_DNI_REVERSO, 'R-');

        // Solo DNIs que existen en las 3 carpetas
        $dnisCompletos = $dnisFotoCarnet->intersect($dnisDniAnverso)->intersect($dnisDniReverso)->values();

        // Excluir DNIs que ya están en la carpeta indicada (ya fueron validados)
        if ($excludeFolder) {
            $dnisExcluir = $this->extractDnisFromFolder($disk, $excludeFolder . Constants::CARPETA_FOTO_CARNET, '');
            $dnisCompletos = $dnisCompletos->diff($dnisExcluir)->values();
        }

        $query = $this->model->newQuery()->with('files');
        $query->whereIn('num_documento', $dnisCompletos)
              ->where('estado_postulante_id', $estadoId);
        $query->applyFilters($request);
        $query->applySort($request);

        return $query->applyPagination($request);
    }

    private function extractDnisFromFolder(string $disk, string $directory, string $prefix): \Illuminate\Support\Collection
    {
        $files = Storage::disk($disk)->files($directory);

        return collect($files)->map(function ($file) use ($prefix) {
            $name = pathinfo($file, PATHINFO_FILENAME);

            if ($prefix !== '' && str_starts_with($name, $prefix)) {
                $name = substr($name, strlen($prefix));
            }

            return $name;
        })->unique();
    }

    public function copyFilesToObserved(string $numDocumento): array
    {
        return $this->copyFilesToFolder($numDocumento, Constants::CARPETA_ARCHIVOS_OBSERVADOS);
    }

    public function copyFilesToValid(string $numDocumento): array
    {
        return $this->copyFilesToFolder($numDocumento, Constants::CARPETA_ARCHIVOS_VALIDOS);
    }

    public function copyFilesToRectified(string $numDocumento): array
    {
        return $this->copyFilesToFolder($numDocumento, Constants::CARPETA_ARCHIVOS_RECTIFICADOS);
    }

    private function copyFilesToFolder(string $numDocumento, string $destFolder): array
    {
        $disk = Constants::DISK_STORAGE;
        $copied = [];

        $fileMappings = [
            [
                'source' => Constants::RUTA_FOTO_CARNET_INSCRIPTO,
                'dest' => $destFolder . Constants::CARPETA_FOTO_CARNET,
                'prefix' => '',
            ],
            [
                'source' => Constants::RUTA_DNI_ANVERSO_INSCRIPTO,
                'dest' => $destFolder . Constants::CARPETA_DNI_ANVERSO,
                'prefix' => 'A-',
            ],
            [
                'source' => Constants::RUTA_DNI_REVERSO_INSCRIPTO,
                'dest' => $destFolder . Constants::CARPETA_DNI_REVERSO,
                'prefix' => 'R-',
            ],
        ];

        foreach ($fileMappings as $mapping) {
            $fileName = $mapping['prefix'] . $numDocumento;
            $sourceFile = $this->findFileByName($disk, $mapping['source'], $fileName);

            if (!$sourceFile) {
                continue;
            }

            $extension = pathinfo($sourceFile, PATHINFO_EXTENSION);
            $destPath = $mapping['dest'] . $fileName . '.' . $extension;

            Storage::disk($disk)->copy($sourceFile, $destPath);
            $copied[] = $destPath;
        }

        if (empty($copied)) {
            throw new Exception('No se encontraron archivos del postulante con documento: ' . $numDocumento);
        }

        return $copied;
    }

    private function findFileByName(string $disk, string $directory, string $fileName): ?string
    {
        $files = Storage::disk($disk)->files($directory);

        foreach ($files as $file) {
            $nameWithoutExt = pathinfo($file, PATHINFO_FILENAME);
            if ($nameWithoutExt === $fileName) {
                return $file;
            }
        }

        return null;
    }

    public function checkRegistration(array $data): array
    {
        $bank = Bank::where('num_documento', $data['num_documento'])
            ->where('num_doc_depo', $data['num_doc_depo'])
            ->whereNotNull('postulant_id')
            ->with('postulant')
            ->first();

        if (!$bank || !$bank->postulant) {
            throw new Exception('No se encontró un postulante registrado con los datos proporcionados.');
        }

        $postulant = $bank->postulant->load('files');

        // Generar token de rectificación con 5 minutos de expiración
        $expirationTime = time() + (Constants::TOKEN_EXPIRATION_MINUTES * 60);

        $payload = [
            'postulant_id' => $postulant->id,
            'bank_id' => $bank->id,
            'num_documento' => $data['num_documento'],
            'num_doc_depo' => $data['num_doc_depo'],
            'type' => 'rectification',
            'iat' => time(),
            'exp' => $expirationTime,
        ];

        $token = JWT::encode($payload, config('app.key'), 'HS256');

        return [
            'postulant' => $postulant,
            'token_rectificacion' => $token,
            'expires_in' => Constants::TOKEN_EXPIRATION_MINUTES * 60,
            'expires_at' => date('Y-m-d H:i:s', $expirationTime),
        ];
    }

    public function rectifyFiles(array $data, string $token): Postulant
    {
        $payload = $this->bankService->validateRectificationToken($token);

        $postulant = $this->model->find($payload->postulant_id);

        if (!$postulant) {
            throw new Exception('Postulante no encontrado.');
        }

        $numDocumento = $postulant->num_documento;

        if (isset($data['foto_postulante'])) {
            $this->softDeletePreviousFiles($postulant, ['foto_postulante', 'foto_postulante_rectificado']);
            $this->attachFile(
                $postulant, $data['foto_postulante'], 'image', 'foto_postulante_rectificado',
                Constants::CARPETA_ARCHIVOS_RECTIFICADOS . Constants::CARPETA_FOTO_CARNET, $numDocumento
            );
        }

        if (isset($data['dni_anverso'])) {
            $this->softDeletePreviousFiles($postulant, ['foto_dni_anverso', 'foto_dni_anverso_rectificado']);
            $this->attachFile(
                $postulant, $data['dni_anverso'], 'image', 'foto_dni_anverso_rectificado',
                Constants::CARPETA_ARCHIVOS_RECTIFICADOS . Constants::CARPETA_DNI_ANVERSO, 'A-' . $numDocumento
            );
        }

        if (isset($data['dni_reverso'])) {
            $this->softDeletePreviousFiles($postulant, ['foto_dni_reverso', 'foto_dni_reverso_rectificado']);
            $this->attachFile(
                $postulant, $data['dni_reverso'], 'image', 'foto_dni_reverso_rectificado',
                Constants::CARPETA_ARCHIVOS_RECTIFICADOS . Constants::CARPETA_DNI_REVERSO, 'R-' . $numDocumento
            );
        }

        // Actualizar estado a envío observado
        $postulant->update(['estado_postulante_id' => PostulantState::ARCHIVOS_ENVIO_OBSERVADOS]);

        return $postulant->fresh();
    }

    private function softDeletePreviousFiles(Postulant $postulant, array $typeEntities): void
    {
        $postulant->files()
            ->whereIn('type_entitie', $typeEntities)
            ->each(fn(File $file) => $file->delete());
    }

    private function attachFile(Postulant $postulant, $uploadedFile, string $type, string $typeEntitie, string $directory, string $customName): void
    {
        // Subir archivo usando FileService
        $file = $this->fileService->upload(
            $postulant,
            $uploadedFile,
            true, // isPublic
            $type,
            $typeEntitie,
            $directory,
            $customName
        );
    }

    public function requestUpdatePostulant(array $data): array{

        $bank = Bank::where('num_documento', $data['num_documento'])
            ->where('num_doc_depo', $data['num_doc_depo'])
            ->whereNotNull('postulant_id')
            ->with('postulant')
            ->first();

        if (!$bank || !$bank->postulant) {
            throw new Exception('No se encontró un postulante registrado con los datos proporcionados.');
        }

        $postulant = $bank->postulant;

        $hasPending = UpdateRequest::where('postulant_id', $postulant->id)
            ->where('status', UpdateRequest::STATUS_PENDING)
            ->exists();

        if ($hasPending) {
            throw new Exception('Ya tiene una solicitud de actualización pendiente. Debe esperar a que sea atendida antes de generar una nueva.');
        }

        $existingRequest = UpdateRequest::where('postulant_id', $postulant->id)
            ->where('status', UpdateRequest::STATUS_PENDING)
            ->where('code_expires_at', '>', now())
            ->first();

        if ($existingRequest) {
            throw new Exception('Ya tiene una solicitud de actualización pendiente con un código único vigente. Debe usar el código enviado a su correo para realizar la actualización o esperar a que expire antes de generar una nueva solicitud.');
        }

        // Generar token de actualización con 5 minutos de expiración
        $expirationTime = time() + (Constants::TOKEN_EXPIRATION_MINUTES_REQUEST * 60);

        $payload = [
            'postulant_id' => $postulant->id,
            'bank_id' => $bank->id,
            'num_documento' => $data['num_documento'],
            'num_doc_depo' => $data['num_doc_depo'],
            'type' => 'update_request',
            'iat' => time(),
            'exp' => $expirationTime,
        ];

        $token = JWT::encode($payload, config('app.key'), 'HS256');

        return [
            'postulant' => $postulant,
            'token_solicitud_actualizacion' => $token,
            'expires_in' => Constants::TOKEN_EXPIRATION_MINUTES_REQUEST * 60,
            'expires_at' => date('Y-m-d H:i:s', $expirationTime),
        ];
    }

    public function createRequestUpdate(array $data, string $token): array
    {
        $payload = $this->bankService->validateRequestUpdateToken($token);

        $postulant = $this->model->find($payload->postulant_id);

        if (!$postulant) {
            throw new Exception('Postulante no encontrado.');
        }

        $hasPending = UpdateRequest::where('postulant_id', $postulant->id)
            ->where('status', UpdateRequest::STATUS_PENDING)
            ->exists();

        if ($hasPending) {
            throw new Exception('Ya tiene una solicitud de actualización pendiente. Debe esperar a que sea atendida antes de generar una nueva.');
        }

        $uniqueCode = Str::uuid()->toString();
        $expiresAt  = now()->addHours(Constants::CODIGO_UNICO_EXPIRATION_HOURS);

        UpdateRequest::create([
            'postulant_id'    => $postulant->id,
            'status'          => UpdateRequest::STATUS_PENDING,
            'reason'          => $data['reason'],
            'unique_code'     => $uniqueCode,
            'code_used'       => false,
            'code_expires_at' => $expiresAt,
        ]);

        return [
            'message'    => 'Su solicitud ha sido registrada. Se ha enviado un código de acceso al correo registrado, válido por 24 horas.',
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
        ];
    }

    public function checkRequestUpdatePostulant (array $data): array {
        $updateRequest = UpdateRequest::where('unique_code', $data['unique_code'])
            ->where('code_used', false)
            ->where('code_expires_at', '>', now())
            ->with('postulant')
            ->first();

        if (!$updateRequest) {
            throw new Exception('Código único inválido o expirado. Por favor, genere una nueva solicitud de actualización.');
        }

        $postulant = $updateRequest->postulant;

        if (!$postulant) {
            throw new Exception('Postulante asociado a la solicitud de actualización no encontrado.');
        }

        // Generar token de acceso para actualización con 5 minutos de expiración
        $expirationTime = time() + (Constants::TOKEN_EXPIRATION_MINUTES_UPDATE * 60);

        $payload = [
            'postulant_id' => $postulant->id,
            'update_request_id' => $updateRequest->id,
            'type' => 'update_data_postulant',
            'iat' => time(),
            'exp' => $expirationTime,
        ];

        $token = JWT::encode($payload, config('app.key'), 'HS256');

        return [
            'postulant' => $updateRequest->postulant,
            'token_actualizacion_postulante' => $token,
            'expires_in' => Constants::TOKEN_EXPIRATION_MINUTES_UPDATE * 60,
            'expires_at' => date('Y-m-d H:i:s', $expirationTime),
        ];
    }

    public function updatePostulantData(array $data, string $token): Postulant
    {
        $payload = $this->bankService->validateUpdateDataToken($token);

        $postulant = $this->model->find($payload->postulant_id);

        if (!$postulant) {
            throw new Exception('Postulante no encontrado.');
        }

        $updateRequest = UpdateRequest::find($payload->update_request_id);

        if (!$updateRequest || $updateRequest->code_used) {
            throw new Exception('La solicitud de actualización ya fue utilizada o no es válida.');
        }

        $allowedFields = [
            'nombres_apoderado',
            'ap_paterno_apoderado',
            'ap_materno_apoderado',
            'anno_egreso',
            'telefono',
            'telefono_ap',
            'direccion',
            'colegio_id',
        ];

        $updateData = array_filter(
            array_intersect_key($data, array_flip($allowedFields)),
            fn($value) => !is_null($value)
        );

        if (empty($updateData)) {
            throw new Exception('Debe proporcionar al menos un campo para actualizar.');
        }

        $oldValues = array_intersect_key($postulant->toArray(), $updateData);

        $postulant->update($updateData);

        $updateRequest->update([
            'code_used'  => true,
            'old_values' => $oldValues,
            'new_values' => $updateData,
        ]);

        return $postulant->fresh();
    }
}
