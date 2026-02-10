<?php

namespace App\Http\Services;

use App\Http\Utils\Constants;
use App\Models\Postulant;
use App\Models\Process;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    public function generateData(Postulant $postulant): string
    {
        $process = Process::getProcessNumber();

        return implode('-', [
            $postulant->nombres,
            $postulant->ap_paterno,
            $postulant->ap_materno,
            "DNI=" . $postulant->num_documento,
            "ADMISION $process:{$postulant->programa_academico_id}",
            $postulant->modalidad_id,
        ]);
    }

    public function save(Postulant $postulant): string
    {
        $data = $this->generateData($postulant);
        $filename = 'QR' . md5($postulant->num_documento) . '.svg';

        $qrCode = QrCode::encoding('UTF-8')->generate($data);
        Storage::disk(Constants::DISK_STORAGE)->put(Constants::RUTA_FOTO_QR . $filename, $qrCode);

        return $filename;
    }
}