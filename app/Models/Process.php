<?php

namespace App\Models;

use App\Http\Traits\Auditable;
use App\Http\Traits\FlexibleQueries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Process extends Model
{
    /** @use HasFactory<\Database\Factories\ProcessFactory> */
    use HasFactory, Auditable, FlexibleQueries, softDeletes;

    protected $table = 'tb_proceso';

    protected $fillable = [
        'numero',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'estado' => 'boolean',
    ];

    public function setNumeroAttribute($value)
    {
        $this->attributes['numero'] = strtoupper($value);
    }

    public function setDescripcionAttribute($value)
    {
        $this->attributes['descripcion'] = strtoupper($value);
    }

    protected function getFilterConfig(): array
    {
        return [
            'search' => [
                'type' => 'global_search',
                'columns' => ['numero', 'descripcion'],
            ],
            'estado' => [
                'columns' => ['estado'],
            ],
            'fecha_inicio' => [
                'type' => 'date_range',
                'columns' => ['fecha_inicio'],
            ],
            'fecha_fin' => [
                'type' => 'date_range',
                'columns' => ['fecha_fin'],
            ],
        ];
    }

    protected function getSortConfig(): array
    {
        return [
            'allowed' => ['numero', 'descripcion', 'fecha_inicio', 'fecha_fin', 'estado', 'created_at', 'updated_at'],
            'default' => 'id'
        ];
    }
}
