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

    /**
     * @throws \Exception
     */
    public function VerifyPayment(Request $request)
    {
        $rules = [
            'num_doc_depo' => 'required|string',
            'num_documento' => 'required|string',
            'num_oficina' => 'required|string',
            'fecha' => 'required|date',
            'tipo_colegio' => 'required|string|in:Nacional,Particular',
        ];

        $this->validateRequest($request, $rules);

        $result = $this->service->verifyPayment($request->only(array_keys($rules)));

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    private function validateRequest(Request $request, array $rules)
    {
        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first());
        }

            return null;
    }
}
