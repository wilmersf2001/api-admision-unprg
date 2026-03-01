<?php

namespace App\Http\Notifications;

use App\Http\Contracts\NotificationProvider;
use App\Models\UpdateRequest;

class UpdateRequestNotification implements NotificationProvider
{
    public function toNotification(): ?array
    {
        $count = UpdateRequest::where('status', UpdateRequest::STATUS_PENDING)->count();

        if ($count === 0) {
            return null;
        }

        return [
            'type'  => 'update_request',
            'label' => 'Solicitudes de actualización pendientes',
            'count' => $count,
            'route' => '/admin/solicitudes-actualizacion',
        ];
    }
}