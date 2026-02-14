<?php

namespace App\Models;

use App\Http\Traits\Auditable;
use App\Http\Traits\FlexibleQueries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DistributionVacancies extends Model
{
    /** @use HasFactory<\Database\Factories\DistributionVacanciesFactory> */
    use HasFactory, Auditable, FlexibleQueries, softDeletes;

    protected $table = 'tb_distribucion_vacantes';

    protected $fillable = [
        'vacantes',
        'estado',
        'programa_academico_id',
        'modalidad_id',
        'sede_id',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    protected function getFilterConfig(): array
    {
        return [
            'search' => [
                'type' => 'global_search',
                'columns' => ['vacantes'],
            ],
            'estado' => [
                'columns' => ['estado'],
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
        ];
    }

    protected function getSortConfig(): array
    {
        return [
            'allowed' => ['vacantes', 'estado', 'created_at', 'updated_at'],
            'default' => 'id'
        ];
    }

    public function academicProgram()
    {
        return $this->belongsTo(AcademicProgram::class, 'programa_academico_id');
    }

    public function modality()
    {
        return $this->belongsTo(Modality::class, 'modalidad_id');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }
}
