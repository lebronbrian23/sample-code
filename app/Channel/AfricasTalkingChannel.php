<?php

namespace SampleProject\Channels;

use AfricasTalking\SDK\AfricasTalking;
use Illuminate\Notifications\Notification;
use SampleProject\Channel\AfricasTalkingMessage;


class AfricasTalkingChannel
{
    public function __construct(AfricasTalking $africasTalking )
    {
        $this->africasTalking = $africasTalking;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {

        if (! $to = $notifiable->routeNotificationFor('AfricasTalking')) {
            return;
        }
        $message = $notification->toAfricasTalking($notifiable);
        if (is_string($message)) {
            $message = new AfricasTalkingMessage($message);
        }
        $this->africasTalking->sms();
        $this->africasTalking->send($to , $message->getContent());
    }

}