<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDistributionVacanciesRequest;
use App\Http\Requests\UpdateDistributionVacanciesRequest;
use App\Http\Resources\DistributionVacanciesResource;
use App\Http\Services\DistributionVacanciesService;
use App\Http\Traits\ApiResponse;
use App\Http\Traits\HandlesValidation;
use App\Models\DistributionVacancies;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DistributionVacanciesController extends Controller
{
    use ApiResponse, HandlesValidation;

    protected DistributionVacanciesService $service;
    private string $nameModel = 'Distribución de Vacantes';

    public function __construct(DistributionVacanciesService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $data = $this->service->getFiltered($request);

        if (method_exists($data, 'getCollection')) {
            $data->setCollection($data->getCollection()->map(fn($item) => new DistributionVacanciesResource($item)));
        } else {
            $data = $data->map(fn($item) => new DistributionVacanciesResource($item));
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function store(StoreDistributionVacanciesRequest $request)
    {
        try {
            $data = $request->validated();
            $createdModel = $this->service->create($data);
            return $this->successResponse(new DistributionVacanciesResource($createdModel), $this->nameModel . " creado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(DistributionVacancies $distributionVacancies)
    {
        try {
            return $this->successResponse(new DistributionVacanciesResource($distributionVacancies), $this->nameModel . " obtenido exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateDistributionVacanciesRequest $request, DistributionVacancies $distributionVacancies)
    {
        try {
            $data = $request->validated();
            $updatedModel = $this->service->update($distributionVacancies->id, $data);
            return $this->successResponse(new DistributionVacanciesResource($updatedModel), $this->nameModel . " actualizado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function upsert(StoreDistributionVacanciesRequest $request)
    {
        try {
            $data = $request->validated();
            $result = $this->service->upsert($data);
            return $this->successResponse(new DistributionVacanciesResource($result), $this->nameModel . " guardado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(DistributionVacancies $distributionVacancies)
    {
        try {
            $this->service->delete($distributionVacancies->id);
            return $this->successResponse(null, $this->nameModel . " eliminado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
