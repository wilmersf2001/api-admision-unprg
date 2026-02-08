<?php

namespace App\Models;

use App\Http\Traits\Auditable;
use App\Http\Traits\FlexibleQueries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicGroup extends Model
{
    /** @use HasFactory<\Database\Factories\AcademicGroupFactory> */
    use HasFactory, Auditable, FlexibleQueries, softDeletes;

    protected $table = 'tb_grupo_academico';

    protected $fillable = [
        'letra',
        'nombre',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function setLetraAttribute($value)
    {
        $this->attributes['letra'] = strtoupper($value);
    }

    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = strtoupper($value);
    }

    protected function getFilterConfig(): array
    {
        return [
            'search' => [
                'type' => 'global_search',
                'columns' => ['letra', 'nombre'],
            ],
            'estado' => [
                'columns' => ['estado'],
            ],
        ];
    }

    protected function getSortConfig(): array
    {
        return [
            'allowed' => ['letra', 'nombre', 'estado', 'created_at', 'updated_at'],
            'default' => 'id'
        ];
    }
}
