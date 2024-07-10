<?php

namespace App\Notifications\Channels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;
use RuntimeException;

class DatabaseChannel
{
    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     * @return Model
     */
    public function send($notifiable, Notification $notification)
    {
        $data = $this->getData($notifiable, $notification);

        \App\Models\Notification::create([
            'title'           => $data['title'],
            'message'         => $data['message'] ?? '',
            'notifiable_type' => $notifiable::class,
            'notifiable_id'   => $notifiable->id,
            'sends_at'        => now(),
        ]);
    }

    /**
     * Get the data for the notification.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     * @return array
     *
     * @throws RuntimeException
     */
    protected function getData($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toDatabase')) {
            return is_array($data = $notification->toDatabase($notifiable))
                ? $data : $data->data;
        }

        if (method_exists($notification, 'toArray')) {
            return $notification->toArray($notifiable);
        }

        throw new RuntimeException('Notification is missing toDatabase / toArray method.');
    }
}
