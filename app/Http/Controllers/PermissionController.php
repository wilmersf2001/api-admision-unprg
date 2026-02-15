<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMultiplePermissionRoleRequest;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Http\Services\PermissionService;
use App\Http\Traits\ApiResponse;
use App\Http\Traits\HandlesValidation;
use App\Models\Permission;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionController extends Controller
{
    use ApiResponse, HandlesValidation;

    protected PermissionService $service;
    private string $nameModel = 'Permiso';

    public function __construct(PermissionService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $data = $this->service->getFiltered($request);

        if (method_exists($data, 'getCollection')) {
            $data->setCollection($data->getCollection()->map(fn($item) => new PermissionResource($item)));
        } else {
            $data = $data->map(fn($item) => new PermissionResource($item));
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function bulkSync(StorePermissionRequest $request)
    {
        try {
            $data = $request->validated();
            $result = $this->service->bulkSync($data);

            return $this->successResponse(
                $result,
                $result->message
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                $exception->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function saveToRole(StoreMultiplePermissionRoleRequest $request)
    {
        try {
            return $this->successResponse($this->service->savePermissionsToRole(
                $request->role_id,
                $request->permissions
            ));
        } catch (Exception $exception) {
            return $this->errorResponse('Error al guardar permisos al rol', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function show(Permission $permission)
    {
        try {
            return $this->successResponse(new PermissionResource($permission), $this->nameModel . " obtenido exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al obtener ' . $this->nameModel, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function removeFromRole(Request $request)
    {
        try {
            $request->validate([
                'role_id' => 'required|exists:roles,id',
                'permission_id' => 'required|exists:permissions,id',
            ]);

            $this->service->removePermissionFromRole(
                $request->role_id,
                $request->permission_id
            );

            return $this->successResponse(null, $this->nameModel . " eliminado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
