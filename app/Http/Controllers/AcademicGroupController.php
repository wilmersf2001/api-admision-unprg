<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAcademicGroupRequest;
use App\Http\Requests\UpdateAcademicGroupRequest;
use App\Http\Resources\AcademicGroupResource;
use App\Http\Services\AcademicGroupService;
use App\Http\Traits\ApiResponse;
use App\Http\Traits\HandlesValidation;
use App\Models\AcademicGroup;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AcademicGroupController extends Controller
{
    use ApiResponse, HandlesValidation;

    protected AcademicGroupService $service;
    private string $nameModel = 'Grupo Académico';

    public function __construct(AcademicGroupService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $data = $this->service->getFiltered($request);

        if (method_exists($data, 'getCollection')) {
            $data->setCollection($data->getCollection()->map(fn($item) => new AcademicGroupResource($item)));
        } else {
            $data = $data->map(fn($item) => new AcademicGroupResource($item));
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function store(StoreAcademicGroupRequest $request)
    {
        try {
            $data = $request->validated();
            $createdModel = $this->service->create($data);
            return $this->successResponse(new AcademicGroupResource($createdModel), $this->nameModel . " creado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al crear ' . $this->nameModel, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(AcademicGroup $academicGroup)
    {
        try {
            return $this->successResponse(new AcademicGroupResource($academicGroup), $this->nameModel . " obtenido exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al obtener ' . $this->nameModel, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateAcademicGroupRequest $request, AcademicGroup $academicGroup)
    {
        try {
            $data = $request->validated();
            $updatedModel = $this->service->update($academicGroup->id, $data);
            return $this->successResponse(new AcademicGroupResource($updatedModel), $this->nameModel . " actualizado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al actualizar ' . $this->nameModel, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(AcademicGroup $academicGroup)
    {
        try {
            $this->service->delete($academicGroup->id);
            return $this->successResponse(null, $this->nameModel . " eliminado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al eliminar ' . $this->nameModel, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
