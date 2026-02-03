<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFacultyRequest;
use App\Http\Requests\UpdateFacultyRequest;
use App\Http\Resources\FacultyResource;
use App\Http\Services\FacultyService;
use App\Http\Traits\ApiResponse;
use App\Http\Traits\HandlesValidation;
use App\Models\Faculty;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FacultyController extends Controller
{
    use ApiResponse, HandlesValidation;

    protected FacultyService $service;
    private string $nameModel = 'Facultad';

    public function __construct(FacultyService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $data = $this->service->getFiltered($request);

        if (method_exists($data, 'getCollection')) {
            $data->setCollection($data->getCollection()->map(fn($item) => new FacultyResource($item)));
        } else {
            $data = $data->map(fn($item) => new FacultyResource($item));
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function store(StoreFacultyRequest $request)
    {
        try {
            $data = $request->validated();
            $createdModel = $this->service->create($data);
            return $this->successResponse(new FacultyResource($createdModel), $this->nameModel . " creado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al crear ' . $exception, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Faculty $faculty)
    {
        try {
            return $this->successResponse(new FacultyResource($faculty), $this->nameModel . " obtenido exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al obtener ' . $this->nameModel, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateFacultyRequest $request, Faculty $faculty)
    {
        try {
            $data = $request->validated();
            $updatedModel = $this->service->update($faculty->id, $data);
            return $this->successResponse(new FacultyResource($updatedModel), $this->nameModel . " actualizado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al actualizar ' . $this->nameModel, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Faculty $faculty)
    {
        try {
            $this->service->delete($faculty->id);
            return $this->successResponse(null, $this->nameModel . " eliminado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al eliminar ' . $this->nameModel, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
