<?php

namespace App\Http\Utils;

use App\Models\District;
use App\Models\Postulant;
use App\Models\Process;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class UtilFunction
{
    public static function formatearLocalizacion($localizacion)
    {
        if (is_array($localizacion)) {
            return $localizacion["departamento"] . ' | ' . $localizacion["provincia"] . ' | ' . $localizacion["distrito"];
        }

        return $localizacion;
    }

  public static function getLocationByDistrito($id)
  {
    $district = District::with('province.department')->findOrFail($id);
    $districtName = $district->nombre;
    $provinceName = $district->province->nombre;
    $departmentName = $district->province->department->nombre;
    return [
      'departamento' => $departmentName,
      'provincia' => $provinceName,
      'distrito' => $districtName
    ];
  }

  public static function getDateToday()
  {
    $today = Carbon::now()->locale('es_PE');
    $formattedDate = $today->isoFormat('D [de] MMMM [del] YYYY');
    return $formattedDate;
  }

    public static function getImagePathByDni(Postulant $postulante)
    {
        $urlPhotoValid = Constants::CARPETA_ARCHIVOS_VALIDOS . Constants::CARPETA_FOTO_CARNET . $postulante->num_documento . '.jpeg';

        if (Storage::disk(Constants::DISK_STORAGE)->exists($urlPhotoValid)) {
            $dniPath = Storage::url($urlPhotoValid);

            if (in_array($postulante->estado_postulante_id, Constants::ESTADOS_VALIDOS_POSTULANTE_ADMISION)) {
                return $dniPath;
            }
        }

        return $dniPath;
    }

    public static function dataQr(Postulant $postulante)
    {
        $process = Process::getProcessNumber();

        $response = implode('-', [
            $postulante->nombres,
            $postulante->ap_paterno,
            $postulante->ap_materno,
            "DNI=" . $postulante->num_documento,
            "ADMISION $process:{$postulante->programa_academico_id}",
            $postulante->modalidad_id
        ]);
        return $response;
    }

    public static function saveQr(array $requestApplicant)
    {
        $processNumber = Process::getProcessNumber();
        $nameQr = 'QR' . md5($requestApplicant['num_documento']);
        $dataQr = implode('-', [
            $requestApplicant['nombres'],
            $requestApplicant['ap_paterno'],
            $requestApplicant['ap_materno'],
            "DNI=" . $requestApplicant['num_documento'],
            "ADMISION $processNumber:{$requestApplicant['programa_academico_id']}",
            $requestApplicant['modalidad_id'],
        ]);
        $qrCode = QrCode::encoding('UTF-8')->generate($dataQr);
        $filename = $nameQr . '.svg';
        Storage::disk(Constants::DISK_STORAGE)->put(Constants::RUTA_FOTO_QR . $filename, $qrCode);
    }
}
