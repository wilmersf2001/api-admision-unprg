<?php

namespace App\Http\Services;

use App\Http\Contracts\NotificationProvider;
use App\Http\Notifications\UpdateRequestNotification;

class NotificationService
{
    /** @var NotificationProvider[] */
    protected array $providers;

    public function __construct()
    {
        $this->providers = [
            new UpdateRequestNotification(),
            // Registra aquí futuros proveedores:
            // new ObservedPostulantNotification(),
            // new PendingPaymentNotification(),
        ];
    }

    public function getNotifications(): array
    {
        $data = [];

        foreach ($this->providers as $provider) {
            $notification = $provider->toNotification();

            if ($notification !== null) {
                $data[] = $notification;
            }
        }

        return [
            'data'        => $data,
            'total_count' => array_sum(array_column($data, 'count')),
        ];
    }
}