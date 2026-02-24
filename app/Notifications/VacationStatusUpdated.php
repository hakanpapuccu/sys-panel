<?php

namespace App\Notifications;

use App\Models\Vacation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VacationStatusUpdated extends Notification
{
    use Queueable;

    public $vacation;

    public $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(Vacation $vacation, string $status)
    {
        $this->vacation = $vacation;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $message = $this->status === 'approved'
            ? 'İzin talebiniz onaylandı.'
            : 'İzin talebiniz reddedildi.';

        return [
            'vacation_id' => $this->vacation->id,
            'status' => $this->status,
            'message' => $message,
            'url' => route('vacations'), // User goes to vacations page to see status
        ];
    }
}
