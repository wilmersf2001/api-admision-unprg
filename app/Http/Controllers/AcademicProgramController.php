<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAcademicProgramRequest;
use App\Http\Requests\UpdateAcademicProgramRequest;
use App\Http\Resources\AcademicProgramResource;
use App\Http\Services\AcademicProgramService;
use App\Http\Traits\ApiResponse;
use App\Http\Traits\HandlesValidation;
use App\Models\AcademicProgram;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AcademicProgramController extends Controller
{
    use ApiResponse, HandlesValidation;

    protected AcademicProgramService $service;
    private string $nameModel = 'AcademicProgramen';

    public function __construct(AcademicProgramService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $data = $this->service->getFiltered($request);

        if (method_exists($data, 'getCollection')) {
            $data->setCollection($data->getCollection()->map(fn($item) => new AcademicProgramResource($item)));
        } else {
            $data = $data->map(fn($item) => new AcademicProgramResource($item));
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function store(StoreAcademicProgramRequest $request)
    {
        try {
            $data = $request->validated();
            $createdModel = $this->service->create($data);
            return $this->successResponse(new AcademicProgramResource($createdModel), $this->nameModel . " creado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al crear ' . $this->nameModel, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(AcademicProgram $academicProgram)
    {
        try {
            return $this->successResponse(new AcademicProgramResource($academicProgram), $this->nameModel . " obtenido exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al obtener ' . $this->nameModel, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateAcademicProgramRequest $request, AcademicProgram $academicProgram)
    {
        try {
            $data = $request->validated();
            $updatedModel = $this->service->update($academicProgram->id, $data);
            return $this->successResponse(new AcademicProgramResource($updatedModel), $this->nameModel . " actualizado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al actualizar ' . $this->nameModel, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(AcademicProgram $academicProgram)
    {
        try {
            $this->service->delete($academicProgram->id);
            return $this->successResponse(null, $this->nameModel . " eliminado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al eliminar ' . $this->nameModel, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
