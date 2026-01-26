<?php

namespace App\Models;

use App\Http\Traits\Auditable;
use App\Http\Traits\FlexibleQueries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    /** @use HasFactory<\Database\Factories\CountryFactory> */
    use HasFactory, Auditable, FlexibleQueries, softDeletes;

    protected $table = 'tb_pais';

    protected $fillable = [
        'nombre',
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
                'columns' => ['nombre'],
            ],
            'estado' => [
                'columns' => ['estado'],
            ],
        ];
    }

    protected function getSortConfig(): array
    {
        return [
            'allowed' => ['nombre', 'estado', 'created_at', 'updated_at'],
            'default' => 'id'
        ];
    }
}
