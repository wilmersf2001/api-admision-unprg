<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCountryRequest;
use App\Http\Requests\UpdateCountryRequest;
use App\Http\Resources\CountryResource;
use App\Http\Services\CountryService;
use App\Http\Traits\ApiResponse;
use App\Http\Traits\HandlesValidation;
use App\Models\Country;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CountryController extends Controller
{
    use ApiResponse, HandlesValidation;

    protected CountryService $service;
    private string $nameModel = 'País';

    public function __construct(CountryService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $data = $this->service->getFiltered($request);

        if (method_exists($data, 'getCollection')) {
            $data->setCollection($data->getCollection()->map(fn($item) => new CountryResource($item)));
        } else {
            $data = $data->map(fn($item) => new CountryResource($item));
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function store(StoreCountryRequest $request)
    {
        try {
            $data = $request->validated();
            $createdModel = $this->service->create($data);
            return $this->successResponse(new CountryResource($createdModel), $this->nameModel . " creado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al crear ' . $this->nameModel, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Country $country)
    {
        try {
            return $this->successResponse(new CountryResource($country), $this->nameModel . " obtenido exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al obtener ' . $this->nameModel, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateCountryRequest $request, Country $country)
    {
        try {
            $data = $request->validated();
            $updatedModel = $this->service->update($country->id, $data);
            return $this->successResponse(new CountryResource($updatedModel), $this->nameModel . " actualizado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al actualizar ' . $this->nameModel, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Country $country)
    {
        try {
            $this->service->delete($country->id);
            return $this->successResponse(null, $this->nameModel . " eliminado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al eliminar ' . $this->nameModel, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
