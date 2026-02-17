<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTxtFileRequest;
use App\Http\Resources\TxtFileResource;
use App\Http\Services\TxtFileService;
use App\Http\Traits\ApiResponse;
use App\Http\Traits\HandlesValidation;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TxtFileController extends Controller
{
    use ApiResponse, HandlesValidation;

    protected TxtFileService $service;
    private string $nameModel = 'Archivo Txt';

    public function __construct(TxtFileService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $data = $this->service->getFiltered($request);

        if (method_exists($data, 'getCollection')) {
            $data->setCollection($data->getCollection()->map(fn($item) => new TxtFileResource($item)));
        } else {
            $data = $data->map(fn($item) => new TxtFileResource($item));
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function store(StoreTxtFileRequest $request)
    {
        try {
            $data = $request->validated();
            $createdModel = $this->service->create($data);
            return $this->successResponse(new TxtFileResource($createdModel), $this->nameModel . " creado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
