<?php

namespace App\Models;

use App\Http\Traits\Auditable;
use App\Http\Traits\FlexibleQueries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modality extends Model
{
    /** @use HasFactory<\Database\Factories\ModalityFactory> */
    use HasFactory, Auditable, FlexibleQueries, softDeletes;

    protected $table = 'tb_modalidad';

    protected $fillable = [
        'descripcion',
        'monto_nacional',
        'monto_particular',
        'estado',
        'examen_id',
    ];

    protected $casts = [
        'monto_nacional' => 'decimal:2',
        'monto_particular' => 'decimal:2',
        'estado' => 'boolean',
    ];

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
            'examen_id' => [
                'columns' => ['examen_id'],
            ],
        ];
    }

    protected function getSortConfig(): array
    {
        return [
            'allowed' => ['descripcion', 'monto_nacional', 'monto_particular', 'estado', 'created_at', 'updated_at'],
            'default' => 'id'
        ];
    }

    public function setDescripcionAttribute($value)
    {
        $this->attributes['descripcion'] = strtoupper($value);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'examen_id');
    }
}
