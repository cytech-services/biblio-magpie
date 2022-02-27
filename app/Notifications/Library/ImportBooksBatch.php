<?php

namespace App\Notifications\Library;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ImportBooksBatch extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The job status.
     *
     * @var array
     */
    protected $status;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(array $status)
    {
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast'];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return (new BroadcastMessage($this->status))->onQueue('notifications');
    }
}
