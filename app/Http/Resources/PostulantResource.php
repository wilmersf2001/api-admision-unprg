<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostulantResource extends JsonResource
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
            'nombre' => $this->nombre,
            'ap_paterno' => $this->ap_paterno,
            'ap_materno' => $this->ap_materno,
            'fecha_nacimiento' => $this->fecha_nacimiento,
            'num_documento' => $this->num_documento,
            'tipo_documento' => $this->tipo_documento,
            'num_documento_apoderado' => $this->num_documento_apoderado,
            'nombres_apoderado' => $this->nombres_apoderado,
            'ap_paterno_apoderado' => $this->ap_paterno_apoderado,
            'ap_materno_apoderado' => $this->ap_materno_apoderado,
            'num_voucher' => $this->num_voucher,
            'direccion' => $this->direccion,
            'correo' => $this->correo,
            'telefono' => $this->telefono,
            'telefono_ap' => $this->telefono_ap,
            'anno_egreso' => $this->anno_egreso,
            'fecha_inscripcion' => $this->fecha_inscripcion,
            'num_veces_unprg' => $this->num_veces_unprg,
            'num_veces_otros' => $this->num_veces_otros,
            'codigo' => $this->codigo,
            'ingreso' => $this->ingreso,
            'sexo_id' => $this->sexo_id,
            'distrito_nac_id' => $this->distrito_nac_id,
            'distrito_res_id' => $this->distrito_res_id,
            'tipo_direccion_id' => $this->tipo_direccion_id,
            'programa_academico_id' => $this->programa_academico_id,
            'colegio_id' => $this->colegio_id,
            'universidad_id' => $this->universidad_id,
            'modalidad_id' => $this->modalidad_id,
            'sede_id' => $this->sede_id,
            'pais_id' => $this->pais_id,
            'estado_postulante_id' => $this->estado_postulante_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
