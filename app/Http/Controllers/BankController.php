<?php

namespace App\Http\Controllers;

use App\Http\Resources\BankResource;
use App\Http\Services\BankService;
use App\Http\Traits\ApiResponse;
use App\Http\Traits\HandlesValidation;
use Illuminate\Http\Request;

class BankController extends Controller
{
    use ApiResponse, HandlesValidation;

    protected BankService $service;
    private string $nameModel = 'Banco';

    public function __construct(BankService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $data = $this->service->getFiltered($request);

        if (method_exists($data, 'getCollection')) {
            $data->setCollection($data->getCollection()->map(fn($item) => new BankResource($item)));
        } else {
            $data = $data->map(fn($item) => new BankResource($item));
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
