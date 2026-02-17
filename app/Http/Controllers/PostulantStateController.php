<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostulantStateRequest;
use App\Http\Requests\UpdatePostulantStateRequest;
use App\Http\Resources\PostulantStateResource;
use App\Http\Services\PostulantStateService;
use App\Http\Traits\ApiResponse;
use App\Http\Traits\HandlesValidation;
use App\Models\PostulantState;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PostulantStateController extends Controller
{
    use ApiResponse, HandlesValidation;

    protected PostulantStateService $service;
    private string $nameModel = 'Estado de Postulante';

    public function __construct(PostulantStateService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $data = $this->service->getFiltered($request);

        if (method_exists($data, 'getCollection')) {
            $data->setCollection($data->getCollection()->map(fn($item) => new PostulantStateResource($item)));
        } else {
            $data = $data->map(fn($item) => new PostulantStateResource($item));
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function store(StorePostulantStateRequest $request)
    {
        try {
            $data = $request->validated();
            $createdModel = $this->service->create($data);
            return $this->successResponse(new PostulantStateResource($createdModel), $this->nameModel . " creado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(PostulantState $postulantState)
    {
        try {
            return $this->successResponse(new PostulantStateResource($postulantState), $this->nameModel . " obtenido exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdatePostulantStateRequest $request, PostulantState $postulantState)
    {
        try {
            $data = $request->validated();
            $updatedModel = $this->service->update($postulantState->id, $data);
            return $this->successResponse(new PostulantStateResource($updatedModel), $this->nameModel . " actualizado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(PostulantState $postulantState)
    {
        try {
            $this->service->delete($postulantState->id);
            return $this->successResponse(null, $this->nameModel . " eliminado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
