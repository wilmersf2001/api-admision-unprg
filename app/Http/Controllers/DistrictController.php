<?php

namespace App\Http\Controllers;

use App\Http\Resources\DistrictResource;
use App\Http\Services\DistrictService;
use App\Http\Traits\ApiResponse;
use App\Http\Traits\HandlesValidation;
use App\Models\District;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    use ApiResponse, HandlesValidation;

    protected DistrictService $service;

    public function __construct(DistrictService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $data = $this->service->getFiltered($request);

        if (method_exists($data, 'getCollection')) {
            $data->setCollection($data->getCollection()->map(fn($item) => new DistrictResource($item)));
        } else {
            $data = $data->map(fn($item) => new DistrictResource($item));
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
