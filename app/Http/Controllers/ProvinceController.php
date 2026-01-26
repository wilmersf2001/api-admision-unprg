<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProvinceResource;
use App\Http\Services\ProvinceService;
use App\Http\Traits\ApiResponse;
use App\Http\Traits\HandlesValidation;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    use ApiResponse, HandlesValidation;

    protected ProvinceService $service;

    public function __construct(ProvinceService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $data = $this->service->getFiltered($request);

        if (method_exists($data, 'getCollection')) {
            $data->setCollection($data->getCollection()->map(fn($item) => new ProvinceResource($item)));
        } else {
            $data = $data->map(fn($item) => new ProvinceResource($item));
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
