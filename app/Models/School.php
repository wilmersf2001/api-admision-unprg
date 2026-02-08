<?php

namespace App\Models;

use App\Http\Traits\Auditable;
use App\Http\Traits\FlexibleQueries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    /** @use HasFactory<\Database\Factories\SchoolFactory> */
    use HasFactory, Auditable, FlexibleQueries, softDeletes;

    protected $table = 'tb_colegio';

    protected $fillable = [
        'nombre',
        'centro_poblado',
        'tipo',
        'ubigeo',
        'distrito_id',
    ];

    protected function getFilterConfig(): array
    {
        return [
            'search' => [
                'type' => 'global_search',
                'columns' => ['nombre', 'centro_poblado', 'ubigeo'],
            ],
            'tipo' => [
                'columns' => ['tipo'],
            ],
            'distrito_id' => [
                'columns' => ['distrito_id'],
            ],
            'provincia_id' => [
                'type' => 'relation',
                'relation' => 'district',
                'column' => 'provincia_id',
            ],
            'departamento_id' => [
                'type' => 'relation',
                'relation' => 'district.province',
                'column' => 'departamento_id',
            ],
        ];
    }

    protected function getSortConfig(): array
    {
        return [
            'allowed' => ['nombre', 'centro_poblado', 'tipo', 'ubigeo', 'created_at', 'updated_at'],
            'default' => 'id'
        ];
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'distrito_id');
    }
}
