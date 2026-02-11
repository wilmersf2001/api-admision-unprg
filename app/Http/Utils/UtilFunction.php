<?php

namespace App\Http\Utils;

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

  public static function getDateToday()
  {
    $today = Carbon::now()->locale('es_PE');
    $formattedDate = $today->isoFormat('D [de] MMMM [del] YYYY');
    return $formattedDate;
  }
}
