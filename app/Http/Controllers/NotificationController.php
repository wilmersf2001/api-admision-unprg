<?php

namespace App\Http\Controllers;

use App\Http\Services\NotificationService;
use App\Http\Traits\ApiResponse;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
    use ApiResponse;

    protected NotificationService $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        try {
            return $this->successResponse(
                $this->service->getNotifications(),
                'Notificaciones obtenidas exitosamente'
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}