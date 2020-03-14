<?php

namespace SampleProject;

use Illuminate\Database\Eloquent\Model;

class WantedSasa extends Model
{
    public static function sendSMS($phone_number, $message){
        $sms_url = "http://sms.infosis.uk:8866/cgi-bin/sendsms";
        $sms_username = "infosis";
        $sms_password = "infosis";
        $sms_senderid = "GoBigHub";

        $full_sms_url = $sms_url;
        $full_sms_url .= "?username=".urlencode($sms_username);
        $full_sms_url .= "&password=".urlencode($sms_password);
        $full_sms_url .= "&from=".urlencode($sms_senderid);
        $full_sms_url .= "&to=".urlencode($phone_number);
        $full_sms_url .= "&text=".urlencode($message);

        return $response = file_get_contents($full_sms_url);

    }

    public static function createFileNames()
    {
        $name = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $alpha = str_split($name);
        shuffle($alpha);
        $alpha = array_slice($alpha, 0, 5);
        $initial = implode('', $alpha);

        return $initial;
    }

    //function for generating a pin.
    public static function generatePIN($digits){
        $i = 0;
        $pin = "";
        while($i < $digits){
            $pin .= mt_rand(0, 9);
            $i++;
        }
        return $pin;
    }

    //function for generating account number.
    public static function generateAccountN0(){
        $year = '20'.date('y');
        $accounts =  User::count();
        return $year.($accounts + 1 ) ;
    }
    //function for generating account number.
    public static function generateItemNo(){
        $year = '20'.date('y');
        $items =  Item::count();
        return $year.($items + 1 ) ;
    }
}
