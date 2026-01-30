<?php

namespace App\Http\Controllers;

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
    private string $nameModel = 'Proceso';

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
}
