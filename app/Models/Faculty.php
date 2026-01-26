<?php

namespace App\Models;

use App\Http\Traits\Auditable;
use App\Http\Traits\FlexibleQueries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faculty extends Model
{
    /** @use HasFactory<\Database\Factories\FacultyFactory> */
    use HasFactory, Auditable, FlexibleQueries, softDeletes;

    protected $table = 'tb_facultad';

    protected $fillable = [
        'codigo',
        'nombre',
        'abreviatura',
        'decano',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    protected function getFilterConfig(): array
    {
        return [
            'search' => [
                'type' => 'global_search',
                'columns' => ['codigo', 'nombre', 'abreviatura', 'decano'],
            ],
            'estado' => [
                'columns' => ['estado'],
            ],
        ];
    }

    protected function getSortConfig(): array
    {
        return [
            'allowed' => ['codigo', 'nombre', 'abreviatura', 'decano', 'estado', 'created_at', 'updated_at'],
            'default' => 'id'
        ];
    }
}
