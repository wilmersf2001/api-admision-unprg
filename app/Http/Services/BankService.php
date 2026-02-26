<?php

namespace App\Http\Services;

use App\Http\Utils\Constants;
use App\Http\Utils\UtilFunction;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Bank;
use App\Models\Process;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\Request;

class BankService
{
    protected Bank $model;

    public function __construct(Bank $model)
    {
        $this->model = $model;
    }

    public function getFiltered(Request $request)
    {
        $query = $this->model->newQuery();
        $query->applyFilters($request);
        $query->applySort($request);
        return $query->applyPagination($request);
    }

    public function getPaymentTotals(Request $request): array
    {
        $query = $this->model->newQuery();
        $query->applyFilters($request);

        // Obtener totales y montos agrupados por concepto
        $totalesPorConcepto = (clone $query)
            ->selectRaw('cod_concepto, importe, COUNT(*) as total, SUM(importe) as monto')
            ->groupBy('cod_concepto', 'importe')
            ->get();

        $totalesFormatted = [];
        $totalGeneral = 0;
        $montoTotalGeneral = 0;

        foreach ($totalesPorConcepto as $concepto) {
            $totalesFormatted[$concepto->cod_concepto] = [
                'importe' => (float) $concepto->importe,
                'total' => $concepto->total,
                'monto_total' => (float) $concepto->monto,
            ];
            $totalGeneral += $concepto->total;
            $montoTotalGeneral += $concepto->monto;
        }

        return [
            'totales_por_concepto' => $totalesFormatted,
            'total_general' => $totalGeneral,
            'monto_total_general' => (float) $montoTotalGeneral,
        ];
    }

    /**
     * Verifica el pago y genera un token de inscripción si es válido
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function verifyPayment(array $data): array
    {
        // Buscar el registro de pago
        $record = $this->model
            ->where('num_doc_depo', $data['num_doc_depo'])
            ->where('num_documento', $data['num_documento'])
            ->where('num_oficina', $data['num_oficina'])
            ->whereDate('fecha', $data['fecha'])
            ->first();

        if (!$record) {
            throw new Exception('Pago no encontrado. Verifique los datos ingresados.');
        }

        // Verificar que el pago NO esté usado
        if ($record->isUsed()) {
            throw new Exception('Este pago ya fue utilizado para una inscripción.');
        }

        // Validar monto según tipo de colegio
        $montoEsperado = $data['tipo_colegio'] === 'Nacional'
            ? Bank::MONTO_NACIONAL
            : Bank::MONTO_PARTICULAR;

        if ((float)$record->importe !== $montoEsperado) {
            throw new Exception('El monto del pago no coincide para colegio ' . $data['tipo_colegio']);
        }

        // Generar token de inscripción
        $expirationTime = time() + (Constants::TOKEN_EXPIRATION_MINUTES * 60);

        $payload = [
            'bank_id' => $record->id,
            'num_documento' => $data['num_documento'],
            'num_doc_depo' => $data['num_doc_depo'],
            'tipo_colegio' => $data['tipo_colegio'],
            'iat' => time(),
            'exp' => $expirationTime,
        ];

        $token = JWT::encode($payload, config('app.key'), 'HS256');

        return [
            'verified' => true,
            'token_inscripcion' => $token,
            'expires_in' => Constants::TOKEN_EXPIRATION_MINUTES * 60, // segundos
            'expires_at' => date('Y-m-d H:i:s', $expirationTime),
        ];
    }

    /**
     * Valida un token de inscripción y retorna los datos del payload
     *
     * @param string $token
     * @return object
     * @throws Exception
     */
    public function validateInscriptionToken(string $token): object
    {
        try {
            $payload = JWT::decode($token, new Key(config('app.key'), 'HS256'));

            // Verificar que el banco asociado aún no esté usado
            $bank = $this->model->find($payload->bank_id);

            if (!$bank) {
                throw new Exception('El pago asociado al token no existe.');
            }

            if ($bank->isUsed()) {
                throw new Exception('Este pago ya fue utilizado para una inscripción.');
            }

            return $payload;

        } catch (ExpiredException $e) {
            throw new Exception('El tiempo para inscribirse ha expirado. Verifique su pago nuevamente.');
        } catch (Exception $e) {
            if (str_contains($e->getMessage(), 'expirado') || str_contains($e->getMessage(), 'utilizado')) {
                throw $e;
            }
            throw new Exception('Token de inscripción inválido.');
        }
    }

    /**
     * Obtiene un registro de banco por ID con bloqueo para transacción
     *
     * @param int $bankId
     * @return Bank
     * @throws Exception
     */
    public function getBankForRegistration(int $bankId): Bank
    {
        $bank = $this->model
            ->where('id', $bankId)
            ->lockForUpdate()
            ->first();

        if (!$bank) {
            throw new Exception('Pago no encontrado.');
        }

        if ($bank->isUsed()) {
            throw new Exception('Este pago ya fue utilizado para una inscripción.');
        }

        return $bank;
    }

    /**
     * Marca un pago como usado por un postulante
     *
     * @param Bank $bank
     * @param int $postulantId
     * @return void
     */
    public function markAsUsed(Bank $bank, int $postulantId): void
    {
        $bank->update([
            'estado' => true,
            'postulant_id' => $postulantId,
            'used_at' => now(),
        ]);
    }

    public function paymentReport(Request $request)
    {
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        $resultadoPagosByMonto = Bank::selectRaw('fecha, importe, COUNT(*) as cantidad_pagos')
            ->whereBetween('fecha', [$dateFrom, $dateTo])
            ->groupBy('fecha', 'importe')
            ->orderBy('fecha', 'desc')
            ->orderBy('importe', 'desc')
            ->get();

        $data = [];
        $importes = [];
        $cantidadImportes = [];
        $procesoAdmision = Process::getProcessNumber();

        foreach ($resultadoPagosByMonto as $pago) {
            $fecha = $pago->fecha->format('Y-m-d');
            $importe = $pago->importe;
            $cantidad_pagos = $pago->cantidad_pagos;

            if (!isset($data[$fecha])) {
                $data[$fecha] = [];
            }
            $data[$fecha][$importe] = $cantidad_pagos;

            if (!in_array($importe, $importes)) {
                $importes[] = $importe;
            }
        }

        foreach ($data as $fecha => $importesData) {
            $subTotal = 0;
            foreach ($importes as $importe) {
                if (isset($importesData[$importe])) {
                    $subTotal += intval($importe) * $importesData[$importe];
                }
            }
            $data[$fecha]['subTotal'] = $subTotal;
        }

        foreach ($importes as $importe) {
            $cantidadImportes[$importe] = 0;
            foreach ($data as $fecha => $importesData) {
                if (isset($importesData[$importe])) {
                    $cantidadImportes[$importe] += $importesData[$importe];
                }
            }
        }

        sort($importes);

        $totalPagos = 0;
        foreach ($importes as $importe) {
            $totalPagos += intval($importe) * $cantidadImportes[$importe];
        }

        $data = [
            'fechaDesde' => $dateFrom,
            'fechaHasta' => $dateTo,
            'importes' => $importes,
            'cantidadImportes' => $cantidadImportes,
            'totalImportes' => array_sum($cantidadImportes),
            'totalPagos' => $totalPagos,
            'data' => $data,
            'today' => UtilFunction::getDateToday(),
            'procesoAdmision' => $procesoAdmision,
            'base64ImageLogoUnprg' => "data:image/png;base64," . base64_encode(file_get_contents(public_path('images/logo_color.png')))
        ];


        return PDF::loadView('reports.pdf-pagos', $data)->stream();
    }
}
