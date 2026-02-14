<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DistributionVacanciesResource extends JsonResource
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
            'vacantes' => $this->vacantes,
            'estado' => $this->estado,
            'programa_academico_id' => $this->programa_academico_id,
            'modalidad_id' => $this->modalidad_id,
            'sede_id' => $this->sede_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
