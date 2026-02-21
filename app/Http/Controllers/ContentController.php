<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContentRequest;
use App\Http\Requests\UpdateContentRequest;
use App\Http\Resources\ContentResource;
use App\Http\Services\ContentService;
use App\Http\Traits\ApiResponse;
use App\Http\Traits\HandlesValidation;
use App\Models\Content;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentController extends Controller
{
    use ApiResponse, HandlesValidation;

    protected ContentService $service;
    private string $nameModel = 'Contenten';

    public function __construct(ContentService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $data = $this->service->getFiltered($request);

        if (method_exists($data, 'getCollection')) {
            $data->setCollection($data->getCollection()->map(fn($item) => new ContentResource($item)));
        } else {
            $data = $data->map(fn($item) => new ContentResource($item));
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function store(StoreContentRequest $request)
    {
        try {
            $data = $request->validated();
            $createdModel = $this->service->create($data);
            return $this->successResponse(new ContentResource($createdModel), $this->nameModel . " creado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Content $content)
    {
        try {
            return $this->successResponse(new ContentResource($content), $this->nameModel . " obtenido exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateContentRequest $request, Content $content)
    {
        try {
            $data = $request->validated();
            $updatedModel = $this->service->update($content->id, $data);
            return $this->successResponse(new ContentResource($updatedModel), $this->nameModel . " actualizado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Content $content)
    {
        try {
            $this->service->delete($content->id);
            return $this->successResponse(null, $this->nameModel . " eliminado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
