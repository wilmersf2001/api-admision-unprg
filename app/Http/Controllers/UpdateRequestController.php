<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUpdateContentRequest;
use App\Http\Requests\UpdateUpdateRequestRequest;
use App\Http\Resources\UpdateRequestResource;
use App\Http\Services\UpdateRequestService;
use App\Http\Traits\ApiResponse;
use App\Http\Traits\HandlesValidation;
use App\Models\UpdateRequest;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateRequestController extends Controller
{
    use ApiResponse, HandlesValidation;

    protected UpdateRequestService $service;
    private string $nameModel = 'Solicitud de Actualización';

    public function __construct(UpdateRequestService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $data = $this->service->getFiltered($request);

        if (method_exists($data, 'getCollection')) {
            $data->setCollection($data->getCollection()->map(fn($item) => new UpdateRequestResource($item)));
        } else {
            $data = $data->map(fn($item) => new UpdateRequestResource($item));
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function respond(UpdateUpdateRequestRequest $request, UpdateRequest $updateRequest)
    {
        try {
            $result = $this->service->respond(
                $updateRequest,
                $request->only(['status', 'note']),
                auth()->id()
            );

            $message = $request->status === UpdateRequest::STATUS_APPROVED
                ? $this->nameModel . ' aprobada y correo enviado al postulante.'
                : $this->nameModel . ' rechazada y correo enviado al postulante.';

            return $this->successResponse($result, $message);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
