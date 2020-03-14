<?php
/**
 * Created by PhpStorm.
 * User: Lebron Brian Cowen
 * Date: 5/5/2018
 * Time: 3:58 PM
 */

namespace SampleProject\Channel;

use AfricasTalking\SDK\AfricasTalking;
use Illuminate\Notifications\Notification;


class MySmsChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $username = 'lebronbrian23';
        $apiKey 	= '923a7673887604904b0991e7c47c0fa9ed709bcd44a00262f70896c79701f86f';

        if (! $to = $notifiable->routeNotificationFor('AfricasTalking')) {
            return;
        }
        $AT = new AfricasTalking($username, $apiKey);
        $message = $notification->toSms($notifiable);
        $options = [
            'message' => $message ,
            'to' => $to,
            //'from' => ,
            'enqueue' => true
        ];
        $SMS = $AT->sms();
        $SMS->send($options);

    }
}