<?php

namespace App\Http\Services;

use App\Http\Utils\Constants;
use App\Models\Bank;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\Request;

class BankService
{
    protected Bank $model;
    private string $nameModel = 'Banco';

    /**
     * Tiempo de expiración del token en minutos
     */
    private const TOKEN_EXPIRATION_MINUTES = 30;

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
            ? Constants::MONTO_NACIONAL
            : Constants::MONTO_PARTICULAR;

        if ($record->importe !== $montoEsperado) {
            throw new Exception('El monto del pago no coincide para colegio ' . $data['tipo_colegio']);
        }

        // Generar token de inscripción
        $expirationTime = time() + (self::TOKEN_EXPIRATION_MINUTES * 60);

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
            'expires_in' => self::TOKEN_EXPIRATION_MINUTES * 60, // segundos
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
            'postulant_id' => $postulantId,
            'used_at' => now(),
        ]);
    }
}
