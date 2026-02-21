<?php

namespace App\Models;

use App\Http\Traits\Auditable;
use App\Http\Traits\FlexibleQueries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicProgram extends Model
{
    /** @use HasFactory<\Database\Factories\AcademicProgramFactory> */
    use HasFactory, Auditable, FlexibleQueries, softDeletes;

    protected $table = 'tb_programa_academico';

    protected $fillable = [
        'codigo',
        'nombre',
        'estado',
        'sede_id',
        'facultad_id',
        'grupo_academico_id',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    protected function getFilterConfig(): array
    {
        return [
            'search' => [
                'type' => 'global_search',
                'columns' => ['codigo', 'nombre'],
            ],
            'estado' => [
                'columns' => ['estado'],
            ],
            'sede_id' => [
                'columns' => ['sede_id'],
            ],
            'facultad_id' => [
                'columns' => ['facultad_id'],
            ],
            'grupo_academico_id' => [
                'columns' => ['grupo_academico_id'],
            ],
        ];
    }

    protected function getSortConfig(): array
    {
        return [
            'allowed' => ['codigo', 'nombre', 'estado', 'created_at', 'updated_at'],
            'default' => 'id'
        ];
    }

    public function setCodigoAttribute($value)
    {
        $this->attributes['codigo'] = strtoupper($value);
    }

    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = strtoupper($value);
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'facultad_id');
    }

    public function academicGroup()
    {
        return $this->belongsTo(AcademicGroup::class, 'grupo_academico_id');
    }
}
