<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AcademicProgramResource extends JsonResource
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
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'estado' => $this->estado,
            'sede_id' => $this->sede_id,
            'sede_name' => $this->sede ? $this->sede->nombre : null,
            'facultad_id' => $this->facultad_id,
            'facultad_name' => $this->faculty ? $this->faculty->nombre : null,
            'grupo_academico_id' => $this->grupo_academico_id,
            'grupo_academico_name' => $this->academicGroup ? $this->academicGroup->nombre : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
