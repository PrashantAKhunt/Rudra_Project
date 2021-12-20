<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Jobs\SendPushNotification;

class GeneralNotification extends Notification {

    use Queueable;

    private $details;
    private $user_ids;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($details, $user_ids) {
        $this->details = $details;
        $this->user_ids = $user_ids;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable) {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable) {
        return (new MailMessage)
                        ->line('The introduction to the notification.')
                        ->action('Notification Action', url('/'))
                        ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable) {

        //send push-notification if needed
        $type_arr = explode(',', $this->details['type']);

        if (in_array('Push', $type_arr)) {
            SendPushNotification::dispatch($this->details, $this->user_ids)->onQueue('push_notification');
        }

        return [
            'type' => $this->details['type'],
            'message' => $this->details['message'],
            'tag' => $this->details['tag'],
            'title'=> $this->details['title']
        ];
    }

}
