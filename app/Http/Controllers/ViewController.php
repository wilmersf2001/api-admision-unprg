<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreViewRequest;
use App\Http\Requests\UpdateViewRequest;
use App\Http\Resources\ViewResource;
use App\Http\Services\ViewService;
use App\Http\Traits\ApiResponse;
use App\Http\Traits\HandlesValidation;
use App\Models\View;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ViewController extends Controller
{
    use ApiResponse, HandlesValidation;

    protected ViewService $service;
    private string $nameModel = 'Vista';

    public function __construct(ViewService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $data = $this->service->getFiltered($request);

        if (method_exists($data, 'getCollection')) {
            $data->setCollection($data->getCollection()->map(fn($item) => new ViewResource($item)));
        } else {
            $data = $data->map(fn($item) => new ViewResource($item));
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Obtiene el árbol completo de views (módulos, submódulos y sub-submódulos)
     * Solo devuelve los módulos activos
     */
    public function getTree()
    {
        $views = View::with('childrenRecursive')
            ->active()
            ->root()
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $this->formatTree($views),
        ]);
    }

    /**
     * Formatea el árbol para la respuesta
     */
    private function formatTree($views)
    {
        return $views->map(function ($view) {
            return [
                'id' => $view->id,
                'name' => $view->name,
                'slug' => $view->slug,
                'route' => $view->route,
                'icon' => $view->icon,
                'description' => $view->description,
                'order' => $view->order,
                'is_active' => $view->is_active,
                'children' => $view->children->isNotEmpty()
                    ? $this->formatTree($view->children)
                    : [],
            ];
        });
    }

    public function store(StoreViewRequest $request)
    {
        try {
            $data = $request->validated();
            $createdModel = $this->service->create($data);
            return $this->successResponse(new ViewResource($createdModel), $this->nameModel . " creado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(View $view)
    {
        try {
            return $this->successResponse(new ViewResource($view), $this->nameModel . " obtenido exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al obtener ' . $this->nameModel, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateViewRequest $request, View $view)
    {
        try {
            $data = $request->validated();
            $updatedModel = $this->service->update($view->id, $data);
            return $this->successResponse(new ViewResource($updatedModel), $this->nameModel . " actualizado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(View $view)
    {
        try {
            $this->service->delete($view->id);
            return $this->successResponse(null, $this->nameModel . " eliminado exitosamente");
        } catch (Exception $exception) {
            return $this->errorResponse('Error al eliminar ' . $this->nameModel, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
