<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DistrictResource extends JsonResource
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
            'ubigeo' => $this->ubigeo,
            'provincia_id' => $this->provincia_id,
            'provincia_nombre' => $this->province?->nombre ?? null,
            'departamento_id' => $this->province?->departamento_id ?? null,
            'departamento_nombre' => $this->province?->department?->nombre ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
