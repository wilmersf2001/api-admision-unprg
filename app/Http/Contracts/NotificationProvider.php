<?php

namespace App\Http\Contracts;

interface NotificationProvider
{
    /**
     * Retorna los datos de notificación o null si no hay nada que notificar.
     *
     * @return array{type: string, label: string, count: int, route: string}|null
     */
    public function toNotification(): ?array;
}
