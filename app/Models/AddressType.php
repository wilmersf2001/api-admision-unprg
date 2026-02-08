<?php

namespace App\Models;

use App\Http\Traits\Auditable;
use App\Http\Traits\FlexibleQueries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AddressType extends Model
{
    /** @use HasFactory<\Database\Factories\AddressTypeFactory> */
    use HasFactory, Auditable, FlexibleQueries, softDeletes;

    protected $table = 'tb_tipo_direccion';

    protected $fillable = [
        'descripcion',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

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
