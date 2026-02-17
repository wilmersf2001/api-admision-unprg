<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAddressTypeRequest;
use App\Http\Requests\UpdateAddressTypeRequest;
use App\Http\Resources\AddressTypeResource;
use App\Http\Services\AddressTypeService;
use App\Http\Traits\ApiResponse;
use App\Http\Traits\HandlesValidation;
use App\Models\AddressType;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddressTypeController extends Controller
{
    use ApiResponse, HandlesValidation;

    protected AddressTypeService $service;
    private string $nameModel = 'Tipo de Dirección';

    public function __construct(AddressTypeService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $data = $this->service->getFiltered($request);

        if (method_exists($data, 'getCollection')) {
            $data->setCollection($data->getCollection()->map(fn($item) => new AddressTypeResource($item)));
        } else {
            $data = $data->map(fn($item) => new AddressTypeResource($item));
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function store(StoreAddressTypeRequest $request)
    {
        try {
            $data = $request->validated();
            $createdModel = $this->service->create($data);
            return $this->successResponse(new AddressTypeResource($createdModel), $this->nameModel . " creado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(AddressType $addressType)
    {
        try {
            return $this->successResponse(new AddressTypeResource($addressType), $this->nameModel . " obtenido exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateAddressTypeRequest $request, AddressType $addressType)
    {
        try {
            $data = $request->validated();
            $updatedModel = $this->service->update($addressType->id, $data);
            return $this->successResponse(new AddressTypeResource($updatedModel), $this->nameModel . " actualizado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(AddressType $addressType)
    {
        try {
            $this->service->delete($addressType->id);
            return $this->successResponse(null, $this->nameModel . " eliminado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
