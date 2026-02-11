<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterPostulantRequest;
use App\Http\Requests\StorePostulantRequest;
use App\Http\Requests\UpdatePostulantRequest;
use App\Http\Resources\PostulantResource;
use App\Http\Services\PostulantService;
use App\Http\Traits\ApiResponse;
use App\Http\Traits\HandlesValidation;
use App\Models\Postulant;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PostulantController extends Controller
{
    use ApiResponse, HandlesValidation;

    protected PostulantService $service;
    private string $nameModel = 'Postulante';

    public function __construct(PostulantService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $data = $this->service->getFiltered($request);

        if (method_exists($data, 'getCollection')) {
            $data->setCollection($data->getCollection()->map(fn($item) => new PostulantResource($item)));
        } else {
            $data = $data->map(fn($item) => new PostulantResource($item));
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Registra un postulante usando el token de inscripción (ruta pública)
     *
     * @param StorePostulantRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePostulantRequest $request)
    {
        // Obtener el token del header
        $token = $request->header('X-Inscription-Token');

        if (!$token) {
            return $this->errorResponse(
                'Token de inscripción requerido. Verifique su pago primero.',
                Response::HTTP_UNAUTHORIZED
            );
        }

        try {
            $data = $request->validated();
            $postulant = $this->service->registerWithToken($data, $token);

            return $this->successResponse(
                [
                    'postulant' => new PostulantResource($postulant),
                    'codigo' => $postulant->codigo,
                    'mensaje' => 'Guarde su código de inscripción: ' . $postulant->codigo
                ],
                'Inscripción completada exitosamente',
                Response::HTTP_CREATED
            );

        } catch (Exception $e) {
            // Determinar el código de respuesta según el tipo de error
            $statusCode = Response::HTTP_BAD_REQUEST;

            if (str_contains($e->getMessage(), 'expirado') ||
                str_contains($e->getMessage(), 'Token') ||
                str_contains($e->getMessage(), 'inválido')) {
                $statusCode = Response::HTTP_UNAUTHORIZED;
            }

            if (str_contains($e->getMessage(), 'utilizado')) {
                $statusCode = Response::HTTP_CONFLICT;
            }

            return $this->errorResponse($e->getMessage(), $statusCode);
        }
    }

    public function show(Postulant $Postulant)
    {
        try {
            return $this->successResponse(new PostulantResource($Postulant), $this->nameModel . " obtenido exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al obtener ' . $this->nameModel, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdatePostulantRequest $request, Postulant $Postulant)
    {
        try {
            $data = $request->validated();
            $updatedModel = $this->service->update($Postulant->id, $data);
            return $this->successResponse(new PostulantResource($updatedModel), $this->nameModel . " actualizado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al actualizar ' . $this->nameModel, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function validFiles(Request $request)
    {
        return $this->respondWithFileList($this->service->getValidFiles($request));
    }

    public function observedFiles(Request $request)
    {
        return $this->respondWithFileList($this->service->getObservedFiles($request));
    }

    public function observedReiteratedFiles(Request $request)
    {
        return $this->respondWithFileList($this->service->getObservedReiteratedFiles($request));
    }

    public function rectifiedFiles(Request $request)
    {
        return $this->respondWithFileList($this->service->getRectifiedFiles($request));
    }

    private function respondWithFileList($data)
    {
        if (method_exists($data, 'getCollection')) {
            $data->setCollection($data->getCollection()->map(fn($item) => new PostulantResource($item)));
        } else {
            $data = $data->map(fn($item) => new PostulantResource($item));
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function copyFilesToObserved(Postulant $postulant)
    {
        try {
            $copied = $this->service->copyFilesToObserved($postulant->num_documento);

            return $this->successResponse(
                ['archivos_copiados' => $copied],
                'Archivos copiados a observados exitosamente'
            );
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function copyFilesToValid(Postulant $postulant)
    {
        try {
            $copied = $this->service->copyFilesToValid($postulant->num_documento);

            return $this->successResponse(
                ['archivos_copiados' => $copied],
                'Archivos copiados a válidos exitosamente'
            );
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function copyFilesToRectified(Postulant $postulant)
    {
        try {
            $copied = $this->service->copyFilesToRectified($postulant->num_documento);

            return $this->successResponse(
                ['archivos_copiados' => $copied],
                'Archivos copiados a rectificados exitosamente'
            );
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function getFile(Postulant $postulant)
    {
        try {
            return $this->service->getFilePDF($postulant->id);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    public function checkRegistration(Request $request)
    {
        $validator = validator($request->all(), [
            'num_documento' => 'required|string',
            'num_doc_depo' => 'required|string',
        ], [
            'num_documento.required' => 'El número de documento es obligatorio.',
            'num_doc_depo.required' => 'El número de documento del depositante es obligatorio.',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first());
        }

        try {
            $result = $this->service->checkRegistration($request->only(['num_documento', 'num_doc_depo']));

            return $this->successResponse([
                'postulant' => new PostulantResource($result['postulant']),
                'token_rectificacion' => $result['token_rectificacion'],
                'expires_in' => $result['expires_in'],
                'expires_at' => $result['expires_at'],
            ], 'Postulante encontrado exitosamente');

        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    public function rectifyFiles(Request $request)
    {
        $validator = validator($request->all(), [
            'foto_postulante' => 'nullable|image|max:2048',
            'dni_anverso' => 'nullable|image|max:2048',
            'dni_reverso' => 'nullable|image|max:2048',
        ], [
            'image' => 'El campo :attribute debe ser una imagen.',
            'max' => 'El campo :attribute no debe superar los 2MB.',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first());
        }

        $token = $request->header('X-Rectification-Token');

        if (!$token) {
            return $this->errorResponse(
                'Token de rectificación requerido. Verifique su registro primero.',
                Response::HTTP_UNAUTHORIZED
            );
        }

        try {
            $data = $request->only(['foto_postulante', 'dni_anverso', 'dni_reverso']);
            $postulant = $this->service->rectifyFiles($data, $token);

            return $this->successResponse(
                new PostulantResource($postulant),
                'Archivos rectificados exitosamente'
            );

        } catch (Exception $e) {
            $statusCode = Response::HTTP_BAD_REQUEST;

            if (str_contains($e->getMessage(), 'expirado') || str_contains($e->getMessage(), 'Token') || str_contains($e->getMessage(), 'inválido')) {
                $statusCode = Response::HTTP_UNAUTHORIZED;
            }

            return $this->errorResponse($e->getMessage(), $statusCode);
        }
    }
}
