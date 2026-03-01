<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UpdateRequestResource extends JsonResource
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
            'postulant_id' => $this->postulant_id,
            'status' => $this->status,
            'reason' => $this->reason,
            'note' => $this->note,
            'attended_at' => $this->attended_at?->toDateTimeString(),
            'unique_code' => $this->unique_code,
            'code_used' => $this->code_used,
            'code_expires_at' => $this->code_expires_at?->toDateTimeString(),
            'old_values' => $this->old_values,
            'new_values' => $this->new_values,
            'postulant' => new PostulantResource($this->whenLoaded('postulant')),
            'reviewed_by' => new UserResource($this->whenLoaded('reviewedBy')),
        ];
    }
}
