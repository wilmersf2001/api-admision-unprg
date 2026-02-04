<?php

namespace App\Utils;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use App\Models\Distrito;
use App\Models\Postulante;
use App\Models\Proceso;

class UtilFunction
{
  public static function getLocationByDistrito($id)
  {
    $distrito = Distrito::with('provincia.departamento')->findOrFail($id);
    $namedistrito = $distrito->nombre;
    $nameprovincia = $distrito->provincia->nombre;
    $namedepartamento = $distrito->provincia->departamento->nombre;
    return [
      'departamento' => $namedepartamento,
      'provincia' => $nameprovincia,
      'distrito' => $namedistrito
    ];
  }

  public static function getLocationByPostulante(Postulante $applicant)
  {
    if ($applicant->tipo_documento == 1) {
      return self::getLocationByDistrito($applicant->distrito_nac_id);
    }
    return $applicant->pais->nombre;
  }

  public static function getDateToday()
  {
    $today = Carbon::now()->locale('es_PE');
    $formattedDate = $today->isoFormat('D [de] MMMM [del] YYYY');
    return $formattedDate;
  }

  public static function formatDate($date)
  {
    $date = Carbon::parse($date)->format('Y-m-d');
    return $date;
  }

  public static function getImagePathByDni(Postulante $postulante)
  {
    $urlPhotoValid = Constants::CARPETA_ARCHIVOS_VALIDOS . Constants::CARPETA_FOTO_CARNET . $postulante->num_documento . '.jpg';

    if (Storage::disk(Constants::DISK_STORAGE)->exists($urlPhotoValid)) {
      $dniPath = Storage::url($urlPhotoValid);

      if (in_array($postulante->estado_postulante_id, Constants::ESTADOS_VALIDOS_POSTULANTE_ADMISION)) {
        return $dniPath;
      }
    }

    return $dniPath;
  }

  public static function dataQr(Postulante $postulante)
  {
    $process = Proceso::getProcessNumber();

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
    $processNumber = Proceso::getProcessNumber();
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

  public static function getPhotosObservedByDni(string $dni)
  {
    $pathFolderPhotosObserved = Constants::RUTAS_FOTOS_OBSERVADAS;
    $pathFolderPhotosValid = Constants::RUTAS_FOTOS_VALIDAS;
    $photosObserved = [];
    foreach ($pathFolderPhotosObserved as $i => $pathFolderPhotos) {
      if ($i == 0) {
        $filepath = $pathFolderPhotos . $dni . '.jpg';
        $verificationpath = $pathFolderPhotosValid[$i] . $dni . '.jpg';
      }
      if ($i == 1) {
        $filepath = $pathFolderPhotos . 'A-' . $dni . '.jpg';
        $verificationpath = $pathFolderPhotosValid[$i] . 'A-' . $dni . '.jpg';
      }
      if ($i == 2) {
        $filepath = $pathFolderPhotos . 'R-' . $dni . '.jpg';
        $verificationpath = $pathFolderPhotosValid[$i] . 'R-' . $dni . '.jpg';
      }

      if (Storage::disk(Constants::DISK_STORAGE)->exists($filepath) && !Storage::disk(Constants::DISK_STORAGE)->exists($verificationpath)) {
        $urlPhoto = Storage::url($filepath);
        $photosObserved[] = [
          'url' => $urlPhoto,
          'indicator' => $i,
          'name' => $i == 0 ? 'perfil' : ($i == 1 ? 'anverso' : 'reverso')
        ];
      }
    }
    return $photosObserved;
  }

  public static function photoCarnetExists($dni)
  {
    $urlPhotoValid = Constants::CARPETA_ARCHIVOS_VALIDOS . Constants::CARPETA_FOTO_CARNET . $dni . '.jpg';

    $urlDniAnversoValid = Constants::CARPETA_ARCHIVOS_VALIDOS . Constants::CARPETA_DNI_ANVERSO . 'A-' . $dni . '.jpg';

    $urlDniReversoValid = Constants::CARPETA_ARCHIVOS_VALIDOS . Constants::CARPETA_DNI_REVERSO . 'R-' . $dni . '.jpg';

    $existsPhoto = Storage::disk(Constants::DISK_STORAGE)->exists($urlPhotoValid);
    $existsAnverso = Storage::disk(Constants::DISK_STORAGE)->exists($urlDniAnversoValid);
    $existsReverso = Storage::disk(Constants::DISK_STORAGE)->exists($urlDniReversoValid);

    return ($existsPhoto && $existsAnverso && $existsReverso);
  }

  public static function applicantFilesExisteBackup($dni)
  {
    $urlPhotoValid = Constants::RUTA_FOTO_CARNET_VALIDA_BACKUP . $dni . '.jpg';

    $urlDniAnversoValid = Constants::RUTA_DNI_ANVERSO_VALIDA_BACKUP . 'A-' . $dni . '.jpg';

    $urlDniReversoValid = Constants::RUTA_DNI_REVERSO_VALIDA_BACKUP . 'R-' . $dni . '.jpg';

    $existsPhoto = Storage::disk(Constants::DISK_STORAGE)->exists($urlPhotoValid);
    $existsAnverso = Storage::disk(Constants::DISK_STORAGE)->exists($urlDniAnversoValid);
    $existsReverso = Storage::disk(Constants::DISK_STORAGE)->exists($urlDniReversoValid);

    return $existsPhoto && $existsAnverso && $existsReverso;
  }

  public static function searchFotoCarnetByDni($dni)
  {
    $sourcePathFotoCarnet = Constants::CARPETA_ARCHIVOS_VALIDOS . Constants::CARPETA_FOTO_CARNET;
    $nameFile = "{$dni}.jpg";
    $urlPhotoValid = "{$sourcePathFotoCarnet}{$nameFile}";
    if (Storage::disk(Constants::DISK_STORAGE)->exists($urlPhotoValid)) {
      return Storage::url($urlPhotoValid);
    }
    return 0;
  }

  public static function searchDniAnversoByDni($dni)
  {
    $sourcePathDniAnverso = Constants::CARPETA_ARCHIVOS_VALIDOS . Constants::CARPETA_DNI_ANVERSO;
    $nameFile = "A-{$dni}.jpg";
    $urlPhotoValid = "{$sourcePathDniAnverso}{$nameFile}";
    if (Storage::disk(Constants::DISK_STORAGE)->exists($urlPhotoValid)) {
      return Storage::url($urlPhotoValid);
    }
    return 0;
  }

  public static function searchDniReversoByDni($dni)
  {
    $sourcePathDniReverso = Constants::CARPETA_ARCHIVOS_VALIDOS . Constants::CARPETA_DNI_REVERSO;
    $nameFile = "R-{$dni}.jpg";
    $urlPhotoValid = "{$sourcePathDniReverso}{$nameFile}";
    if (Storage::disk(Constants::DISK_STORAGE)->exists($urlPhotoValid)) {
      return Storage::url($urlPhotoValid);
    }
    return 0;
  }
}
