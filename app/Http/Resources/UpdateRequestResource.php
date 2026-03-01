<?php

namespace App\Http\Resources;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UpdateRequestResource extends JsonResource
{
    private const FIELD_LABELS = [
        'nombres_apoderado'    => 'Nombres del apoderado',
        'ap_paterno_apoderado' => 'Apellido paterno del apoderado',
        'ap_materno_apoderado' => 'Apellido materno del apoderado',
        'anno_egreso'          => 'Año de egreso',
        'telefono'             => 'Teléfono',
        'telefono_ap'          => 'Teléfono del apoderado',
        'direccion'            => 'Dirección',
        'colegio_id'           => 'Colegio',
    ];

    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'postulant_id'   => $this->postulant_id,
            'status'         => $this->status,
            'reason'         => $this->reason,
            'note'           => $this->note,
            'created_at'     => $this->created_at?->toDateTimeString(),
            'attended_at'    => $this->attended_at?->toDateTimeString(),
            'unique_code'    => $this->unique_code,
            'code_used'      => $this->code_used,
            'code_expires_at'=> $this->code_expires_at?->toDateTimeString(),
            'changes'        => $this->formatChanges($this->old_values, $this->new_values),
            'postulant'      => new PostulantResource($this->whenLoaded('postulant')),
            'reviewed_by'    => new UserResource($this->whenLoaded('reviewedBy')),
        ];
    }

    private function formatChanges(?array $old, ?array $new): ?array
    {
        if (empty($old) || empty($new)) {
            return null;
        }

        $schoolCache = [];

        return collect($new)->map(function ($newVal, $field) use ($old, &$schoolCache) {
            $oldVal = $old[$field] ?? null;

            if ($field === 'colegio_id') {
                $oldVal = $this->resolveSchoolName($oldVal, $schoolCache);
                $newVal = $this->resolveSchoolName($newVal, $schoolCache);
            }

            return [
                'campo'  => self::FIELD_LABELS[$field] ?? $field,
                'antes'  => $oldVal,
                'despues'=> $newVal,
            ];
        })->values()->all();
    }

    private function resolveSchoolName(mixed $id, array &$cache): ?string
    {
        if (is_null($id)) {
            return null;
        }

        if (!isset($cache[$id])) {
            $cache[$id] = School::find($id)?->nombre ?? "ID: $id";
        }

        return $cache[$id];
    }
}
