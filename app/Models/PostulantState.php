<?php

namespace App\Models;

use App\Http\Traits\Auditable;
use App\Http\Traits\FlexibleQueries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostulantState extends Model
{
    /** @use HasFactory<\Database\Factories\PostulantStateFactory> */
    use HasFactory, Auditable, FlexibleQueries, softDeletes;

    protected $table = 'tb_estado_postulante';

    protected $fillable = [
        'descripcion',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    const INSCRITO_WEB = 1;
    const ARCHIVOS_OBSERVADOS = 2;
    const ARCHIVOS_ENVIO_OBSERVADOS = 3;
    const VALIDADO_ENVIADO_CORREO = 4;
    const CARNET_IMPRESO_PENDIENTE_ENTREGA = 5;
    const HUELLA_DIGITAL = 6;
    const CARNET_ENTREGADO = 7;
    const INSCRIPCION_ANULADA = 8;

    public function setDescripcionAttribute($value)
    {
        $this->attributes['descripcion'] = strtoupper($value);
    }

    protected function getFilterConfig(): array
    {
        return [
            'search' => [
                'type' => 'global_search',
                'columns' => ['descripcion'],
            ],
            'estado' => [
                'columns' => ['estado'],
            ],
        ];
    }

    protected function getSortConfig(): array
    {
        return [
            'allowed' => ['descripcion', 'estado', 'created_at', 'updated_at'],
            'default' => 'id'
        ];
    }
}
