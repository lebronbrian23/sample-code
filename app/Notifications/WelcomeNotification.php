<?php

namespace SampleProject\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use SampleProject\Channel\AfricasTalkingMessage;
use SampleProject\Channels\AfricasTalkingChannel;
use SampleProject\User;

class WelcomeNotification extends Notification
{
    use Queueable;
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User  $user)
    {
        $this->user = $user;
        //
    }

    /**
     * Get the notification channels.
     *
     * @param  mixed $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
       // return [AfricasTalkingChannel::class];
        return ['database'];

    }

    public function toArray($notifiable)
    {
        return [
            'user_id' => $this->user->id,
            'name' => $this->user->name,
        ];
    }
    /**
     * Get the sms representation of the notification.
     *
     * @param  mixed $notifiable
     * @return Message
     */
    public function toAfricasTalking($notifiable)
    {

        return (new AfricasTalkingMessage)
            ->line('One of your invoices has been paid!');
    }
}
