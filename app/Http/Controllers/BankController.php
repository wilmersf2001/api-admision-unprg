<?php

namespace App\Http\Controllers;

use App\Http\Resources\BankResource;
use App\Http\Services\BankService;
use App\Http\Traits\ApiResponse;
use App\Http\Traits\HandlesValidation;
use App\Models\Bank;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $totals = $this->service->getPaymentTotals($request);

        if (method_exists($data, 'getCollection')) {
            $data->setCollection($data->getCollection()->map(fn($item) => new BankResource($item)));
        } else {
            $data = $data->map(fn($item) => new BankResource($item));
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'totals' => $totals,
        ]);
    }

    /**
     * Verifica el pago y genera un token de inscripción
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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

        $validator = validator($request->all(), $rules, [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'date' => 'El campo :attribute debe ser una fecha válida.',
            'in' => 'El tipo de colegio debe ser Nacional o Particular.',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first());
        }

        try {
            $result = $this->service->verifyPayment($request->only(array_keys($rules)));

            return $this->successResponse($result, 'Pago verificado exitosamente');

        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function paymentReport(Request $request)
    {
            $rules = [
                'date_from' => 'required|date',
                'date_to' => 'required|date|after_or_equal:date_from',
            ];

            $validator = validator($request->all(), $rules, [
                'required' => 'El campo :attribute es obligatorio.',
                'date' => 'El campo :attribute debe ser una fecha válida.',
                'after_or_equal' => 'La fecha hasta debe ser igual o posterior a la fecha desde.',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors()->first());
            }

            try {
                return $this->service->paymentReport($request);
            } catch (Exception $e) {
                return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
            }
    }

    /**
     * Exporta el reporte de pagos a Excel con filtros opcionales
     */
    public function export(Request $request)
    {
        try {
            $query = Bank::query();

            // Aplicar filtros si se proporcionan
            if ($request->has('fecha_start') && $request->has('fecha_end')) {
                $query->whereBetween('fecha', [$request->input('fecha_start'), $request->input('fecha_end')]);
            }

            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('num_doc_depo', 'like', "%$search%")
                      ->orWhere('num_documento', 'like', "%$search%")
                      ->orWhere('num_oficina', 'like', "%$search%");
                });
            }

            $filename = 'reporte_pagos_' . now()->format('Ymd_His') . '.xlsx';

            return Bank::export($filename, $query);

        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
