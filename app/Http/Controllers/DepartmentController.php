<?php

namespace App\Http\Controllers;

use App\Http\Resources\DepartmentResource;
use App\Http\Services\DepartmentService;
use App\Http\Traits\ApiResponse;
use App\Http\Traits\HandlesValidation;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    use ApiResponse, HandlesValidation;

    protected DepartmentService $service;

    public function __construct(DepartmentService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $data = $this->service->getFiltered($request);

        if (method_exists($data, 'getCollection')) {
            $data->setCollection($data->getCollection()->map(fn($item) => new DepartmentResource($item)));
        } else {
            $data = $data->map(fn($item) => new DepartmentResource($item));
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
