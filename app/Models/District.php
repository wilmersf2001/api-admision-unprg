<?php

namespace App\Models;

use App\Http\Traits\Auditable;
use App\Http\Traits\FlexibleQueries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class District extends Model
{
    /** @use HasFactory<\Database\Factories\DistrictFactory> */
    use HasFactory, Auditable, FlexibleQueries, softDeletes;

    protected $table = 'tb_distrito';

    protected $fillable = [
        'nombre',
        'ubigeo',
        'provincia_id',
    ];

    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = strtoupper($value);
    }

    protected function getFilterConfig(): array
    {
        return [
            'search' => [
                'type' => 'global_search',
                'columns' => ['nombre', 'ubigeo'],
            ],
            'provincia_id' => [
                'columns' => ['provincia_id'],
            ],
            'departamento_id' => [
                'type' => 'relation',
                'relation' => 'province',
                'column' => 'departamento_id',
            ],
        ];
    }

    protected function getSortConfig(): array
    {
        return [
            'allowed' => ['nombre', 'ubigeo', 'created_at', 'updated_at'],
            'default' => 'id'
        ];
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'provincia_id');
    }
}
