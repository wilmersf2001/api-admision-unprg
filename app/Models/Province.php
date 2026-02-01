<?php

namespace App\Models;

use App\Http\Traits\Auditable;
use App\Http\Traits\FlexibleQueries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Province extends Model
{
    /** @use HasFactory<\Database\Factories\ProvinceFactory> */
    use HasFactory, Auditable, FlexibleQueries, softDeletes;

    protected $table = 'tb_provincia';

    protected $fillable = [
        'nombre',
        'ubigeo',
        'departamento_id',
    ];

    protected function getFilterConfig(): array
    {
        return [
            'search' => [
                'type' => 'global_search',
                'columns' => ['nombre', 'ubigeo'],
            ],
            'departamento_id' => [
                'columns' => ['departamento_id'],
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

    public function department()
    {
        return $this->belongsTo(Department::class, 'departamento_id');
    }
}
