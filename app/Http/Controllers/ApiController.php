<?php

namespace SampleProject\Http\Controllers;

use AfricasTalking\SDK\AfricasTalking;
use Beyonic;
use Beyonic_Collection_Request;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SampleProject\Item;
use SampleProject\Notifications\WelcomeNotification;
use SampleProject\WantedSasa;
use SampleProject\Notifications\BuyerNotification;
use SampleProject\Notifications\SellerNotification;
use SampleProject\sms;
use SampleProject\SmsPlan;
use SampleProject\Transaction;
use SampleProject\User;

require( 'lib/Beyonic.php' );
Beyonic::setApiKey( 'c4c75674d717102b1a89a3d23f2edbd77077f02e');

class ApiController extends Controller
{
    public function __construct(){
        $this->content = array();
    }
   

}
