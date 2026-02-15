<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ViewResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'route' => $this->route,
            'order' => $this->order,
            'is_active' => $this->is_active,
            'parent_id' => $this->parent_id,
            'permissions' => PermissionResource::collection($this->permissions),
             // Si la vista tiene hijos, los incluye en el recurso
            'children' => $this->whenLoaded('childrenRecursive', function () {
                return ViewResource::collection($this->childrenRecursive);
            }),
        ];
    }
}
