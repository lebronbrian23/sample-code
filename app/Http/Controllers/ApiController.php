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
    /* method to register a user */
    public function Register(Request $request){

        $checkUser = User::where('mobile_no','like', '%'.$request->get('mobile_no').'%')->first();

        if ($checkUser) {
            return response()->json('User with that mobile number already exists' , 404 );
        }

        $user = new User();
        $user->name = $request->get('name');
        $user->user_no = WantedSasa::generateAccountN0();
        $user->mobile_no = $request->get('mobile_no');
        $user->confirmation_code = WantedSasa::generatePIN(5);
        $user->country_code = substr($request->get('mobile_no'), 0, 3);
        $user->ip = request()->ip();
        $user->save();


        $user->makeUser('Client');
        $sms = new Sms();
        $sms->user_id = $user->id;
        $sms->no_of_sms = 5;
        $sms->ip = $request->ip();
        $sms->save();

        $this->content['token'] =  $user->createToken('App')->accessToken;
        $status = 200;

        $user->notify(new WelcomeNotification($user));

        return response()->json($this->content, $status);


    }

    /* method to login user */
    public function login(){

        $check_user = User::where([ ['mobile_no', request('mobile_no') ] , ['active' , 1 ] ])->first();

        if($check_user){
            $user = Auth::loginUsingId($check_user->id);
            $this->content['token'] =  $user->createToken('App')->accessToken;
            $status = 200;
        }
        else{
            $this->content['error'] = 'Unauthorised';
            $status = 401;
        }
        return response()->json($this->content, $status);
    }

    /* method to get logged  in user info  */
    public function getUser()
    {
        $user = User::where('user_no' , Auth::user()->user_no)->first();
        if(!$user) return response()->json('User doesn\'t exist' , 400);
        $user_details = [];
        $user_details['mobile_no'] = $user->mobile_no;
        $user_details['active'] = $user->active;
        $user_details['confirmed'] = $user->confirmed;
        $user_details['country_code'] = $user->country_code;
        $user_details['confirmation_code'] = $user->confirmation_code;
        $user_details['name'] = $user->name;
        $user_details['id'] = $user->id;
        $user_details['user_no'] = $user->user_no;
        $user_details['address'] = $user->address;
        $user_details['created_at'] = $user->created_at;
        $user_details['picture'] = $user->picture;
        return response()->json( $user_details  , 200);
    }
    /* method to get logged  in user info  */
    public function getThisUser($user_no )
    {
        $user = User::where('user_no' , $user_no)->first();
        $user_details = [];
        $user_details['mobile_no'] = $user->mobile_no;
        $user_details['active'] = $user->active;
        $user_details['confirmed'] = $user->confirmed;
        $user_details['country_code'] = $user->country_code;
        $user_details['confirmation_code'] = $user->confirmation_code;
        $user_details['name'] = $user->name;
        $user_details['id'] = $user->id;
        $user_details['user_no'] = $user->user_no;
        $user_details['address'] = $user->address;
        $user_details['created_at'] = $user->created_at;
        $user_details['picture'] = $user->picture;
        return response()->json( $user_details  , 200);
    }
    /* method to update in user info  */
    public function updateProfile(Request $request)
    {
        User::where('id' , Auth::user()->id)
            ->update([
                'name' => $request->get('name') ,
                'address' => $request->get('address') ,
                'ip' => $request->ip() ,
                'picture' => $request->get('picture')
            ]);
        return response()->json('Profile has been updated' , 200);

    }
    /* method to resend code */
    public function resendCode()
    {
        User::where('id' , Auth::user()->id)
            ->update([ 'confirmation_code' => WantedSasa::generatePIN(5) ]);
        return response()->json('We have sent you a confirmation code to '.Auth::user()->mobile_no , 200);
    }
    /* method to verify phone */
    public function confirmCode(Request $request)
    {
        if(Auth::user()->confirmation_code = $request->get('code' ) )
        {
            User::where('id' , Auth::user()->id)
                ->update([ 'confirmed' => 1  ]);

            return response()->json('Success , Code matches' , 200);
        }
        return response()->json('Code doesn\'t match' , 404);

    }
    /* method to get the number of all unread notification  */
    public function getUnreadNotificationsCount()
    {
        return response()->json( Auth::user()->unreadNotifications->count() ,200 );
    }

    /* method to get   all un read  notification  */
    public function getUnreadNotifications()
    {
        $user = User::find(Auth::user()->id);
        $notification_array = [];
        foreach ($user->unreadNotifications as $notification) {
            $tmp = [];
            $tmp['id'] = $notification->id;
            $tmp['type'] = $notification->type;
            $tmp['notifiable_id'] = $notification->notifiable_id;
            $tmp['notifiable_type'] = $notification->notifiable_type;
            $tmp['data'] = $notification->data;
            $tmp['read_at'] = $notification->read_at;
            $tmp['created_at'] = $notification->created_at;
            $tmp['updated_at'] = $notification->updated_at;
            array_push( $notification_array , $tmp) ;
        }
        return response()->json(  $notification_array ,200 );
    }

    /* method to get all  notification  */
    public function getAllNotifications()
    {

        $user = User::find(Auth::user()->id);
        $notification_array = [];
        foreach ($user->notifications as $notification) {
            $tmp = [];
            $tmp['id'] = $notification->id;
            $tmp['type'] = $notification->type;
            $tmp['notifiable_id'] = $notification->notifiable_id;
            $tmp['notifiable_type'] = $notification->notifiable_type;
            $tmp['data'] = $notification->data;
            $tmp['read_at'] = $notification->read_at;
            $tmp['created_at'] = $notification->created_at;
            $tmp['updated_at'] = $notification->updated_at;
            array_push( $notification_array , $tmp) ;
        }
        return response()->json(  $notification_array ,200 );
    }

    /*  mark a notification as read. */
    public function ReadNotification($id ){
        $notification = DB::table('notifications')->where('id' ,  $id )->first();
        if(!$notification) return back()->with('error','notification is not found');

        DB::table('notifications')->where('id' , $id )
            ->update(['read_at' => Carbon::now()]);

        if( $notification->type == 'SampleProject\Notifications\WelcomeNotification' ){
            return  response()->json('Notification has been read' , 200);
        }
    }
    /*  Mark all notifications as read */
    public function MarkAllAsRead()
    {
        $user = User::find(Auth::user()->id);

        $user->unreadNotifications()->update(['read_at' => Carbon::now()]);

        return response()->json('All Notifications have been read' , 200);
    }

    /***********************************
     *
     * Functions for items
     *
     ************************************/
    /*method to post item*/
    public function addItem(Request $request)
    {
        /*if($request->type === 'wanted'){
            $find_items = Item::where([
                ['user_id' , '!=',  Auth::id() ] ,
                ['deleted' ,  0 ],
                ['status' ,  0 ],
                ['type' , 'selling'],
                ['location' ,'like' , '%'.$request->location.'%'] ,
                //['name' ,'like' , '%'.$request->item_name.'%']
            ])->get();
            $users_array = [];
            foreach ($find_items as $item ){
                $user = User::find($item->user_id);
                $logged_user = User::find(Auth::user()->id);
                //$logged_user->notify(new BuyerNotification($item));
                //$user->notify(new SellerNotification($item , $logged_user));
            }

        }
        $username = 'lebronbrian23';
        $apiKey 	= '923a7673887604904b0991e7c47c0fa9ed709bcd44a00262f70896c79701f86f';
        $AT = new AfricasTalking($username, $apiKey);
        $options = [
            'message' => 'here' ,
            'to' => '+256704898421',
            //'from' => ,
            'enqueue' => true
        ];
        $SMS = $AT->sms();
        $SMS->send($options);*/

        $item =  new Item();
        $item->user_id = Auth::user()->id;
        $item->item_no = WantedSasa::generateItemNo();
        $item->name = $request->item_name;
        $item->description = $request->description;
        $item->type = $request->type;
        $item->picture = $request->picture;
        $item->location = $request->location;
        $item->price = $request->price;
        $item->ip = $request->ip();
        $item->save();

        return response()->json('Item Posted' , 200);
    }

    /* get all items */
    public function getItems(Request $request)
    {

        $offset = (int)$request->input('offset', 0);
        $limit = (int)$request->input('limit', 10);
        $search = $request->input('search' ,null);

        $items = Item::select()
            ->latest()
            ->skip($offset)
            ->where( [  ['deleted' ,0], ['type','like', '%'.$search.'%'] ] )
            ->orWhere( [  ['deleted' ,0], ['name','like', '%'.$search.'%'] ] )
            ->orWhere( [  ['deleted' ,0], ['description','like', '%'.$search.'%'] ] )
            ->orWhere( [  ['deleted' ,0], ['location','like', '%'.$search.'%'] ] )
            ->orWhere( [  ['deleted' ,0], ['price','like', '%'.$search.'%'] ] )
            ->take($limit)
            ->orderBy('id', 'desc')
            ->get();
        $items_array = [];

        foreach ($items as $item) {
            $user = User::find($item->user_id);
            $tmp = [];
            $tmp['id'] = $item->id ;
            $tmp['item_no'] = $item->item_no;
            $tmp['seller'] = ucfirst($user->name);
            $tmp['name'] = ucfirst($item->name);
            $tmp['user_id'] = $item->user_id;
            $tmp['loggedUser_id'] = Auth::user()->id;
            $tmp['description'] = ucfirst($item->description);
            $tmp['price'] = number_format($item->price).'/=';
            $tmp['price_2'] = $item->price;
            $tmp['type'] = ucfirst($item->type);
            $tmp['type_2'] = $item->type;
            $tmp['status'] = $item->status;
            $tmp['seller_no'] = $user->user_no;
            $tmp['deleted'] = $item->deleted;
            $tmp['location'] = $item->location;
            $tmp['created_at'] = $item->created_at;
            $tmp['picture'] = $item->picture;

            array_push( $items_array , $tmp);
        }
        unset($items);

        return response()->json( $items_array , 200 );
    }

    /* get  item details  */
    public function getItem($item_no)
    {

        $item = Item::where( [ ['deleted' ,0], ['item_no' , $item_no] ] )->first();
        if(!$item) return response()->json('item not found'  , 404);

            $user = User::find($item->user_id);
            $tmp = [];
            $tmp['id'] = $item->id;
            $tmp['item_no'] = $item->item_no;
            $tmp['seller'] = ucfirst($user->name);
            $tmp['name'] = ucfirst($item->name);
            $tmp['user_id'] = $item->user_id;
            $tmp['loggedUser_id'] = Auth::user()->id;
            $tmp['description'] = ucfirst($item->description);
            $tmp['price'] = number_format($item->price) . '/=';
            $tmp['price_2'] = $item->price;
            $tmp['type'] = ucfirst($item->type);
            $tmp['type_2'] = $item->type;
            $tmp['status'] = $item->status;
            $tmp['seller_no'] = $user->user_no;
            $tmp['deleted'] = $item->deleted;
            $tmp['location'] = $item->location;
            $tmp['created_at'] = $item->created_at;
            $tmp['picture'] = $item->picture;

            unset($item);

            return response()->json($tmp, 200);

    }

    /* get all items */
    public function getMyItems(Request $request)
    {

        $offset = (int)$request->input('offset', 0);
        $limit = (int)$request->input('limit', 10);
        $search = $request->input('search' ,null);

        $items = Item::select()
            ->latest()
            ->skip($offset)
            ->where( [ ['user_id' ,'=', Auth::user()->id ] , ['deleted' ,0], ['type','like', '%'.$search.'%'] ] )
            ->orWhere( [ ['user_id' ,'=', Auth::user()->id ] , ['deleted' ,0], ['name','like', '%'.$search.'%'] ] )
            ->orWhere( [ ['user_id' ,'=', Auth::user()->id ] , ['deleted' ,0], ['description','like', '%'.$search.'%'] ] )
            ->orWhere( [ ['user_id' ,'=', Auth::user()->id ] , ['deleted' ,0], ['location','like', '%'.$search.'%'] ] )
            ->orWhere( [ ['user_id' ,'=', Auth::user()->id ] , ['deleted' ,0], ['price','like', '%'.$search.'%'] ] )
            ->take($limit)
            ->orderBy('id', 'desc')
            ->get();
        $items_array = [];

        foreach ($items as $item) {
            $user = User::find($item->user_id);
            $tmp = [];
            $tmp['id'] = $item->id ;
            $tmp['item_no'] = $item->item_no;
            $tmp['seller'] = ucfirst($user->name);
            $tmp['seller_no'] = $user->user_no;
            $tmp['name'] = ucfirst($item->name);
            $tmp['user_id'] = $item->user_id;
            $tmp['loggedUser_id'] = Auth::user()->id;
            $tmp['description'] = ucfirst($item->description);
            $tmp['price'] = number_format($item->price).'/=';
            $tmp['price_2'] = $item->price;
            $tmp['type'] = ucfirst($item->type);
            $tmp['type_2'] = $item->type;
            $tmp['status'] = $item->status;
            $tmp['deleted'] = $item->deleted;
            $tmp['location'] = $item->location;
            $tmp['created_at'] = $item->created_at;
            $tmp['picture'] = $item->picture;

            array_push( $items_array , $tmp);
        }
        unset($items);

        return response()->json( $items_array , 200 );
    }

    /*method to show an item */
    public function show($id , $item_no)
    {
        $item = Item::where([ [ 'id' , $id] , ['item_no' , $item_no ] ])->first();
        if(!$item) return response()->json('item not found'  , 404);

        $user = User::find($item->user_id);

        $tmp = [];
        $tmp['id'] = $item->id ;
        $tmp['item_no'] = $item->item_no;
        $tmp['name'] = ucfirst($item->name);
        $tmp['user_id'] = $item->user_id;
        $tmp['seller'] = ucfirst($user->name);
        $tmp['seller_no'] = $user->user_no;
        $tmp['description'] = ucfirst($item->description);
        $tmp['price'] = number_format($item->price).'/=';
        $tmp['type'] = ucfirst($item->type);
        $tmp['loggedUser_id'] = Auth::user()->id;
        $tmp['status'] = $item->status;
        $tmp['deleted'] = $item->deleted;
        $tmp['location'] = $item->location;
        $tmp['created_at'] = $item->created_at;
        $tmp['picture'] = $item->picture;

        unset($items);

        return response()->json($tmp , 200);
    }

    /* method to update an item */
    public function updateItem(Request $request, $item_no)
    {
        Item::where([ [ 'item_no' , $item_no]  ])
            ->update([
                'name' => $request->item_name,
                'description' => $request->description,
                'type' => $request->type,
                'picture' => $request->picture,
                'location' => $request->location,
                'price' => $request->price,
                'ip' => $request->ip(),
            ]);
        return response()->json('Item updated' , 200);

    }

    /*method to mark an items unavailable  */
    public function unavailableItem($id)
    {
        Item::where([ [ 'id' , $id]  ])
            ->update([
                'status' => 1 ,
            ]);
        return response()->json('Item is no longer available' , 200);

    }

    /*method to delete an items */
    public function deleteItem($id)
    {
        Item::where([ [ 'id' , $id]  ])
            ->update([
                'deleted' => 1 ,
            ]);
        return response()->json('Item deleted' , 200);

    }

    /***********************************
     *
     * Functions for sms
     *
     ************************************/
    /* method to post item*/
    public function buySms(Request $request)
    {
        return response()->json('This feature is not activated');
        $sms_plan = SmsPlan::where('id' , $request->sms_id)->first();
        $sms =Sms::where('user_id' , Auth::user()->id)->first();

        $collection = Beyonic_Collection_Request::create([
            'phonenumber' => '+' . $request->mobile_no,
            'amount' => $sms_plan->cost,
            'currency' => 'UGX',
            'metadata' => [
                'user_id' => Auth::user()->id,
                'no_of_sms' => ( $sms->no_of_sms + $sms_plan->no_of_sms),
                'ip' => $request->ip(),
            ],
            'send_instructions' => True,
            'callback_url' => 'https://wantedsas.com/beyonic-callback/response',
            'success_message' => 'You have purchased '.$sms_plan->no_of_sms .' SMS '
        ]);
        if ($collection->id) {

            $purchase = new Transaction();
            $purchase->msisdn = $request->mobile_no;
            $purchase->amount = round($sms_plan->cost);
            $purchase->names = Auth::user()->name;
            $purchase->charge = 0;
            $purchase->transaction_id = WantedSasa::generatePIN(5);
            $purchase->transaction_id_from_callback = $collection->id;
            $purchase->payment_reason = 'Purchase Sms';
            $purchase->ip = $request->ip();
            $purchase->paid_by = Auth::user()->id;
            $purchase->transaction_status = $collection->status;
            $purchase->type = 'buying-sms';
            $purchase->save();

        }
        return response()->json('You have just bought '.$sms_plan->no_of_sms .' sms , your sms will be topped up once the transaction is complete ', 200);
    }
    /* method to get all sms count */
    public function getSmsCount()
    {
        $sms = Sms::where('user_id' , Auth::user()->id)->first();

        return response()->json( $sms->no_of_sms  , 200 );
    }
    /* method to get sms cost chart */
    public function getSmsChart()
    {
        $sms = SmsPlan::where('deleted' , 0)->get();
        $sms_array = [];

        foreach ($sms as $item) {

            $tmp = [];
            $tmp['id'] = $item->id ;
            $tmp['cost'] = $item->cost;
            $tmp['no_of_sms'] = ucfirst($item->no_of_sms);
            array_push( $sms_array , $tmp);
        }

        unset($sms);
        return response()->json( $sms_array  , 200 );
    }
    /* method to get sms cost chart */
    public function getSmsCost($id)
    {
        $sms = SmsPlan::where('id' , $id)->first();
        return response()->json( $sms->cost  , 200 );
    }

}
