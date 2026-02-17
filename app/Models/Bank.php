<?php

namespace App\Models;

use App\Http\Traits\Auditable;
use App\Http\Traits\Exportable;
use App\Http\Traits\FlexibleQueries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    /** @use HasFactory<\Database\Factories\BankFactory> */
    use HasFactory, Auditable, FlexibleQueries, softDeletes, Exportable;

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

    public const MONTO_NACIONAL = 280.00;
    public const MONTO_PARTICULAR = 380.00;
    public const CONCEPTO_NACIONAL = '00346';
    public const CONCEPTO_PARTICULAR = '00345';

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

    /**
     * Configuración de columnas para exportación.
     */

    public function getExportColumns(): array
    {
        return [
            'num_oficina' => 'Número de Oficina',
            'cod_concepto' => 'Código de Concepto',
            'tipo_doc_pago' => 'Tipo de Documento de Pago',
            'num_documento' => 'Número de Documento',
            'importe' => 'Importe',
            'fecha' => 'Fecha',
            'hora' => 'Hora',
            'num_doc_depo' => 'Número de Documento de Depósito',
            'tipo_doc_depo' => 'Tipo de Documento de Depósito',
            'observacion_depo' => 'Observación del Depósito',
        ];
    }

    /**
     * Título de la hoja de exportación.
     */
    public function getExportTitle(): string
    {
        return 'Pagos Bancarios';
    }

    /**
     * Query personalizado para exportación con relaciones optimizadas.
     */

    public function getExportQuery()
    {
        return $this->newQuery()->with(['txtFile', 'postulant']);
    }
}
