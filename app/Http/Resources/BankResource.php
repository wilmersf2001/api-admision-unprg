<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'num_oficina' => $this->num_oficina,
            'cod_concepto' => $this->cod_concepto,
            'tipo_doc_pago' => $this->tipo_doc_pago,
            'num_documento' => $this->num_documento,
            'importe' => $this->importe,
            'fecha' => $this->fecha->toDateString(),
            'hora' => $this->hora,
            'estado' => $this->estado,
            'num_doc_depo' => $this->num_doc_depo,
            'tipo_doc_depo' => $this->tipo_doc_depo,
            'observacion_depo' => $this->observacion_depo,
            'archivo_txt_id' => $this->archivo_txt_id,
            'archivo_txt_nombre' => $this->txtFile ? $this->txtFile->nombre : null,
            'is_used' => $this->isUsed(),
            'postulant_id' => $this->postulant_id,
            'used_at' => $this->used_at?->toDateTimeString(),
        ];
    }
}
