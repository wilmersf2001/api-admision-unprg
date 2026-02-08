<?php

namespace App\Models;

use App\Http\Traits\Auditable;
use App\Http\Traits\FlexibleQueries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TxtFile extends Model
{
    /** @use HasFactory<\Database\Factories\TxtFileFactory> */
    use HasFactory, Auditable, FlexibleQueries, softDeletes;

    protected $table = 'tb_archivo_txt';

    protected $fillable = [
        'nombre',
        'cantidad_registros',
        'created_at'
    ];

    protected function getFilterConfig(): array
    {
        return [
            'search' => [
                'type' => 'global_search',
                'columns' => ['nombre', 'cantidad_registros'],
            ],
            'created_at' => [
                'type' => 'date_range',
                'columns' => ['created_at'],
            ],
        ];
    }

    protected function getSortConfig(): array
    {
        return [
            'allowed' => ['nombre', 'cantidad_registros', 'created_at', 'updated_at'],
            'default' => 'id'
        ];
    }
}
