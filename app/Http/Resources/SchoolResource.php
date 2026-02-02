<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolResource extends JsonResource
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
            'centro_poblado' => $this->centro_poblado,
            'tipo' => $this->tipo,
            'ubigeo' => $this->ubigeo,
            'distrito_id' => $this->distrito_id,
            'distrito_nombre' => $this->district?->nombre,
            'provincia_nombre' => $this->district?->province?->nombre,
            'departamento_nombre' => $this->district?->province?->department?->nombre,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
