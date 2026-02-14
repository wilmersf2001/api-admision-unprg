<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Http\Services\RoleService;
use App\Http\Traits\ApiResponse;
use App\Http\Traits\HandlesValidation;
use App\Models\Role;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends Controller
{
    use ApiResponse, HandlesValidation;

    protected RoleService $service;
    private string $nameModel = 'Rol';

    public function __construct(RoleService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $data = $this->service->getFiltered($request);

        if (method_exists($data, 'getCollection')) {
            $data->setCollection($data->getCollection()->map(fn($item) => new RoleResource($item)));
        } else {
            $data = $data->map(fn($item) => new RoleResource($item));
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function recentRole()
    {
        try {
            $role = Role::orderBy('fecha_inicio', 'desc')->first();
            return $this->successResponse($role, "Número de proceso actual obtenido exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al obtener el número de proceso actual', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreRoleRequest $request)
    {
        try {
            $data = $request->validated();
            $createdModel = $this->service->create($data);
            return $this->successResponse(new RoleResource($createdModel), $this->nameModel . " creado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Role $role)
    {
        try {
            return $this->successResponse(new RoleResource($role), $this->nameModel . " obtenido exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al obtener ' . $this->nameModel, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        try {
            $data = $request->validated();
            $updatedModel = $this->service->update($role->id, $data);
            return $this->successResponse(new RoleResource($updatedModel), $this->nameModel . " actualizado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Role $role)
    {
        try {
            $this->service->delete($role->id);
            return $this->successResponse(null, $this->nameModel . " eliminado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al eliminar ' . $this->nameModel, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
