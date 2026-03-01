<?php

namespace App\Http\Services;

use App\Mail\UpdateRequestMail;
use App\Models\UpdateRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UpdateRequestService
{
    protected UpdateRequest $model;

    public function __construct(UpdateRequest $model)
    {
        $this->model = $model;
    }

    public function getFiltered(Request $request)
    {
        $query = $this->model->newQuery()->with(['postulant', 'reviewedBy']);
        $query->applyFilters($request);
        $query->applySort($request);
        return $query->applyPagination($request);
    }

    public function respond(UpdateRequest $updateRequest, array $data, int $reviewedBy): UpdateRequest
    {
        if ($updateRequest->status !== UpdateRequest::STATUS_PENDING) {
            throw new Exception('Esta solicitud ya fue atendida anteriormente.');
        }

        $status = $data['status'];
        $note   = $data['note'] ?? null;

        $updateRequest->update([
            'status'      => $status,
            'note'        => $note,
            'reviewed_by' => $reviewedBy,
            'attended_at' => now(),
        ]);

        $postulant = $updateRequest->postulant;

        if ($status === UpdateRequest::STATUS_APPROVED) {
            Mail::to($postulant->correo)->send(
                new UpdateRequestMail(
                    $postulant,
                    UpdateRequest::STATUS_APPROVED,
                    $updateRequest->unique_code,
                    $updateRequest->code_expires_at->format('d/m/Y H:i')
                )
            );
        } else {
            Mail::to($postulant->correo)->send(
                new UpdateRequestMail(
                    $postulant,
                    UpdateRequest::STATUS_REJECTED,
                    note: $note ?? ''
                )
            );
        }

        return $updateRequest->fresh(['postulant', 'reviewedBy']);
    }
}