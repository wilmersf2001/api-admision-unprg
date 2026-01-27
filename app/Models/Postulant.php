<?php

namespace App\Models;

use App\Http\Traits\Auditable;
use App\Http\Traits\FlexibleQueries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Postulant extends Model
{
    /** @use HasFactory<\Database\Factories\PostulantFactory> */
    use HasFactory, Auditable, FlexibleQueries, softDeletes;

    protected $table = 'tb_postulante';

    protected $fillable = [
        'nombre',
        'ap_paterno',
        'ap_materno',
        'fecha_nacimiento',
        'num_documento',
        'tipo_documento',
        'num_documento_apoderado',
        'nombres_apoderado',
        'ap_paterno_apoderado',
        'ap_materno_apoderado',
        'num_voucher',
        'direccion',
        'correo',
        'telefono',
        'telefono_ap',
        'anno_egreso',
        'fecha_inscripcion',
        'num_veces_unprg',
        'num_veces_otros',
        'codigo',
        'ingreso',
        'sexo_id',
        'distrito_nac_id',
        'distrito_res_id',
        'tipo_direccion_id',
        'programa_academico_id',
        'colegio_id',
        'universidad_id',
        'modalidad_id',
        'sede_id',
        'pais_id',
        'estado_postulante_id',
    ];

    protected function getFilterConfig(): array
    {
        return [
            'search' => [
                'type' => 'global_search',
                'columns' => ['nombre', 'ap_paterno', 'ap_materno', 'num_documento', 'correo', 'codigo'],
            ],
            'programa_academico_id' => [
                'columns' => ['programa_academico_id'],
            ],
            'modalidad_id' => [
                'columns' => ['modalidad_id'],
            ],
            'sede_id' => [
                'columns' => ['sede_id'],
            ],
            'estado_postulante_id' => [
                'columns' => ['estado_postulante_id'],
            ],
        ];
    }

    protected function getSortConfig(): array
    {
        return [
            'allowed' => ['nombre', 'ap_paterno', 'ap_materno', 'fecha_nacimiento', 'num_documento', 'correo', 'codigo', 'created_at', 'updated_at'],
            'default' => 'id'
        ];
    }
}
