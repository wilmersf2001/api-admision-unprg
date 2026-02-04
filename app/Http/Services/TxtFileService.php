<?php

namespace App\Http\Services;

use App\Http\Utils\Constants;
use App\Models\Bank;
use App\Models\TxtFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TxtFileService
{
    protected TxtFile $model;
    private string $nameModel = 'Archivo Txt';

    public function __construct(TxtFile $model)
    {
        $this->model = $model;
    }

    private function processLines($lineas, $archivoTxt)
    {
        $bancoCreated = false;

        foreach ($lineas as $linea) {
            $num_voucher = substr($linea, 18, 7);
            $tipo_doc = substr($linea, 25, 2);
            $concepto = substr($linea, 35, 8);
            $relleno_do = substr($linea, 47, 7);
            $num_doc = substr($linea, 54, 8);
            $importe = intval(substr($linea, 62, 15)) / 100;
            $fecha = substr($linea, 79, 4) . "-" . substr($linea, 83, 2) . "-" . substr($linea, 85, 2);
            $hora = substr($linea, 87, 2) . ":" . substr($linea, 89, 2) . ":" . substr($linea, 91, 2);
            $cod_age = substr($linea, 97, 4);

            if ($tipo_doc == '09') {
                $num_doc = substr($relleno_do . $num_doc, -9);
            }

            if (in_array(ltrim($concepto, '0'), Constants::NUMERO_CONCEPTO_ADMISION)) {
                Bank::create([
                    'num_oficina' => $cod_age,
                    'cod_concepto' => substr($concepto, 3),
                    'tipo_doc_pago' => 1,
                    'num_documento' => $num_voucher,
                    'importe' => $importe,
                    'fecha' => $fecha,
                    'hora' => $hora,
                    'estado' => 0,
                    'num_doc_depo' => $num_doc,
                    'tipo_doc_depo' => $tipo_doc,
                    'archivo_txt_id' => $archivoTxt->id,
                ]);
                $bancoCreated = true;
            }
        }

        $archivoTxt->update([
            'cantidad_registros' => Bank::where('archivo_txt_id', $archivoTxt->id)->count(),
        ]);

        return $bancoCreated;
    }

    public function getFiltered(Request $request)
    {
        $query = $this->model->newQuery(); // Inicia una nueva consulta
        $query->applyFilters($request); // Lee getFilterConfig() y filtra
        $query->applySort($request); // Lee getSortConfig() y ordena
        return $query->applyPagination($request); // all=true = paginado, all=false = sin paginar
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $filetxt = $data['filetxt'];

            if (!$filetxt->isValid()) {
                throw new Exception("El archivo no es valido", Response::HTTP_BAD_REQUEST);
            }

            $datatxt = file_get_contents($filetxt->getRealPath());
            $namefile = strtolower($filetxt->getClientOriginalName());
            $lineas = explode("\n", $datatxt);

            if (TxtFile::where('nombre', $namefile)->exists()) {
                throw new Exception("El archivo ya fue cargado anteriormente", Response::HTTP_CONFLICT);
            }

            $archivoTxt = TxtFile::create([
                'nombre' => $namefile,
                'cantidad_registros' => count($lineas),
            ]);

            $bancoCreated = $this->processLines($lineas, $archivoTxt);

            if (!$bancoCreated) {
                throw new Exception("No se encontraron registros validos en el archivo txt", Response::HTTP_NOT_FOUND);
            }

            DB::commit();

            return $archivoTxt;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
