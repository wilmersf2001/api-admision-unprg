<?php

namespace App\Models;

use App\Http\Traits\Auditable;
use App\Http\Traits\FlexibleQueries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    /** @use HasFactory<\Database\Factories\BankFactory> */
    use HasFactory, Auditable, FlexibleQueries, softDeletes;

    protected $table = 'tb_banco';

    protected $fillable = [
        'num_oficina',
        'cod_concepto',
        'tipo_doc_pago',
        'num_documento',
        'importe',
        'fecha',
        'hora',
        'estado',
        'num_doc_depo',
        'tipo_doc_depo',
        'observacion_depo',
        'archivo_txt_id',
        'postulant_id',
        'used_at',
    ];

    protected $casts = [
        'fecha' => 'date',
        'estado' => 'boolean',
        'used_at' => 'datetime',
    ];

    protected function getFilterConfig(): array
    {
        return [
            'search' => [
                'type' => 'global_search',
                'columns' => ['num_oficina', 'cod_concepto', 'num_documento', 'num_doc_depo'],
            ],
            'estado' => [
                'columns' => ['estado'],
            ],
            'fecha' => [
                'type' => 'date_range',
                'columns' => ['fecha'],
            ],
        ];
    }

    protected function getSortConfig(): array
    {
        return [
            'allowed' => ['num_oficina', 'cod_concepto', 'tipo_doc_pago', 'num_documento', 'importe', 'fecha', 'hora', 'estado', 'num_doc_depo', 'tipo_doc_depo', 'created_at', 'updated_at'],
            'default' => 'id'
        ];
    }

    public function txtFile()
    {
        return $this->belongsTo(TxtFile::class, 'archivo_txt_id');
    }

    public function postulant()
    {
        return $this->belongsTo(Postulant::class, 'postulant_id');
    }

    /**
     * Verifica si el pago ya fue utilizado
     */
    public function isUsed(): bool
    {
        return $this->postulant_id !== null;
    }
}
