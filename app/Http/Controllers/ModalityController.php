<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreModalityRequest;
use App\Http\Requests\UpdateModalityRequest;
use App\Http\Resources\ModalityResource;
use App\Http\Services\ModalityService;
use App\Http\Traits\ApiResponse;
use App\Http\Traits\HandlesValidation;
use App\Models\Modality;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ModalityController extends Controller
{
    use ApiResponse, HandlesValidation;

    protected ModalityService $service;
    private string $nameModel = 'Usuario';

    public function __construct(ModalityService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of Modalitys.
     */
    public function index(Request $request)
    {
        $data = $this->service->getFiltered($request);

        if (method_exists($data, 'getCollection')) {
            $data->setCollection($data->getCollection()->map(fn($item) => new ModalityResource($item)));
        } else {
            $data = $data->map(fn($item) => new ModalityResource($item));
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function store(StoreModalityRequest $request)
    {
        try {
            $data = $request->validated();
            $createdModel = $this->service->create($data);
            return $this->successResponse(new ModalityResource($createdModel), $this->nameModel . " creado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al crear ' . $this->nameModel, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Modality $modality)
    {
        try {
            return $this->successResponse(new ModalityResource($modality), $this->nameModel . " obtenido exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al obtener ' . $this->nameModel, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateModalityRequest $request, Modality $modality)
    {
        try {
            $data = $request->validated();
            $updatedModel = $this->service->update($modality->id, $data);
            return $this->successResponse(new ModalityResource($updatedModel), $this->nameModel . " actualizado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al actualizar ' . $this->nameModel, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Modality $modality)
    {
        try {
            $this->service->delete($modality->id);
            return $this->successResponse(null, $this->nameModel . " eliminado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al eliminar ' . $this->nameModel, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
