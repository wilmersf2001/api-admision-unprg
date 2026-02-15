<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
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
            'view_id' => $this->view_id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'module' => $this->module,
            'policy_method' => $this->policy_method,
            'is_active' => $this->is_active,
             // Incluye la vista relacionada si está cargada
            'view' => new ViewResource($this->whenLoaded('view')),
        ];
    }
}
