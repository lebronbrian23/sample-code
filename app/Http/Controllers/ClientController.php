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
use SampleProject\WantedSasa;
use SampleProject\Notifications\BuyerNotification;
use SampleProject\Notifications\SellerNotification;
use SampleProject\sms;
use SampleProject\SmsPlan;
use SampleProject\Transaction;
use SampleProject\User;

require( 'lib/Beyonic.php' );
Beyonic::setApiKey( 'c4c75674d717102b1a89a3d23f2edbd77077f02e');

class ClientController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('client');

    }

    /* default method after logging in */
    public function index()
    {
        if(Auth::user()->confirmed < 1  ){
            return view('client.confirm-phone');
        }else{
            return view('client.home');
        }

    }

    /* method to show profile view */
    public function profile()
    {
        return view( 'client.profile'  );
    }
    /* method to veiw anotther user's profile view */
    public function getUserProfile($user_no , $name)
    {
        return view( 'client.another-user-profile' ,['user_no' => $user_no , 'name' => $name] );
    }
    /* method to get logged  in user info  */
    public function getUser()
    {
        return response()->json( Auth::user() );
    }
    /* method to get logged  in user info  */
    public function getThisUser($user_no )
    {
        $user = User::where('user_no' , $user_no)->first();
        return response()->json( $user );
    }
    /* method to update in user info  */
    public function updateProfile(Request $request)
    {
        User::where('id' , Auth::user()->id)
            ->update([
                'name' => $request->name ,
                'address' => $request->address ,
                'ip' => $request->ip() ,
                'picture' => $request->picture
            ]);
        return response()->json('Profile has been updated' , 200);

    }
    /* method to resend code */
    public function resendCode()
    {
        User::where('id' , Auth::user()->id)
            ->update([ 'confirmation_code' => WantedSasa::generatePIN(5) ]);
        return redirect('/');
    }
    /* method to verify phone */
    public function confirmCode(Request $request)
    {
        if(Auth::user()->confirmation_code = $request->code ){
            User::where('id' , Auth::user()->id)
                ->update([ 'confirmed' => 1  ]);

            return redirect('/');
        }
        return response()->json('Code doesn\'t match' , 404);

    }
    /* method to get all notification count */
    public function getNotifications()
    {
        return response()->json( Auth::user()->unreadNotifications->count() );
    }
    /* method to load  notifications' view */
     public function showNotifications()
    {
        return view( 'notifications.index');
    }
    /*  mark a notification as read. */
    public function ReadNotification($id , $resource_id ,$resource_no ){
        $notification = DB::table('notifications')->where('id' ,  $id )->first();
        if(!$notification) return back()->with('error','notification is not found');

        DB::table('notifications')->where('id' , $id )
            ->update(['read_at' => Carbon::now()]);

        if( $notification->type == 'SampleProject\Notifications\WelcomeNotification' ){
            return  redirect('/client/profile');
        }
    }
    /*  Mark all notifications as read */
    public function MarkAllAsRead()
    {
        $user = User::find(Auth::user()->id);

        $user->unreadNotifications()->update(['read_at' => Carbon::now()]);

        return redirect()->back()->with('success' ,'All notifications have been read');
    }

    /***********************************
     *
     * Functions for items
     *
     ************************************/
    /*method to post item*/
    public function addItem(Request $request)
    {
        if($request->type === 'wanted'){
            $find_items = Item::where([
                ['user_id' , '!=',  Auth::id() ] ,
                ['deleted' ,  0 ],
                ['status' ,  0 ],
                ['type' , 'selling'],
                ['location' ,'like' , '%'.$request->location.'%'] ,
                ['name' ,'like' , '%'.$request->name.'%']
            ])->get();
            $users_array = [];
            foreach ($find_items as $item ){
                $user = User::find($item->user_id);
                $logged_user = User::find(Auth::user()->id);
                $logged_user->notify(new BuyerNotification($item));
                $user->notify(new SellerNotification($item , $logged_user));
            }

        }
        /*
        $username = 'lebronbrian23';
        $apiKey 	= '923a7673887604904b0991e7c47c0fa9ed709bcd44a00262f70896c79701f86f';
        $AT = new AfricasTalking($username, $apiKey);
        $options = [
            'message' => 'i sent sms  from here' ,
            'to' => '+256704898421',
            //'from' => ,
            'enqueue' => true
        ];
        $SMS = $AT->sms();
        $SMS->send($options);
        */

        $item =  new Item();
        $item->user_id = Auth::user()->id;
        $item->item_no = WantedSasa::generateItemNo();
        $item->name = $request->name;
        $item->description = $request->description;
        $item->type = $request->type;
        $item->picture = $request->picture;
        $item->location = $request->location;
        $item->price = $request->price;
        $item->ip = $request->ip();
        $item->save();

        return response()->json('Item Posted' , 200);
    }

    /*method to fetch all posted items */
    public function getAllItems(Request $request)
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

    /* method to fetch all user items */
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

    /*method to display my items view */
    public function myItems(){
        return view('client.my-items');
    }
    /*method to show an item */
    public function show($id , $item_no)
    {
        $item = Item::where([ [ 'id' , $id] , ['item_no' , $item_no ] ])->first();

        if(!$item) return reaponse()->json('item not found' , 404);

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

        return response()->json($tmp);
    }

    /* method to update an item */
    public function updateItem(Request $request, $id)
    {
        Item::where([ [ 'id' , $id]  ])
            ->update([
                'name' => $request->name,
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
        $item = Item::where([ [ 'id' , $id] ])->first();
        if(!$item) return reaponse()->json('item not found' , 404);

        Item::where([ [ 'id' , $id]  ])
            ->update([
                'status' => 1 ,
            ]);
        return response()->json('Item is no longer available' , 200);

    }

    /*method to delete an item */
    public function deleteItem($id)
    {
        $item = Item::where([[ 'id' , $id] ])->first();
        if(!$item) return reaponse()->json('item not found' , 404);

        if($item->deleted === 1 ) return reaponse()->json('item already deleted' , 404);


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
        $sms_plan = SmsPlan::where('id' , $request->no_of_sms)->first();
        $sms =Sms::where('user_id' , Auth::user()->id)->first();

        try  {
            $purchase = new Transaction();
            $purchase->msisdn = $request->phone;
            $purchase->amount = round($sms_plan->cost);
            $purchase->names = Auth::user()->name;
            $purchase->charge = 0;
            $purchase->transaction_id = WantedSasa::generatePIN(5);
            $purchase->payment_reason = 'Purchase Sms';
            $purchase->ip = $request->ip();
            $purchase->paid_by = Auth::user()->id;
            $purchase->transaction_status = 'Processed';
            $purchase->type = 'buying-sms';
            $purchase->save();

            if($purchase->transaction_status === 'Processed') {
                Sms::where('user_id', Auth::user()->id)
                    ->update([
                        'no_of_sms' => ($sms->no_of_sms + $sms_plan->no_of_sms)
                    ]);
            }
            return response()->json('You have just bought '.$sms_plan->no_of_sms .' sms , your sms will be topped up once the transaction is complete ', 200);
        } catch(\Exception $e) {

            return response()->json( 'Your transaction failed , Something went wrong' , 404);
        }
    }
    /* method to get all sms count */
    public function getSmsCount()
    {
        $sms = Sms::where('user_id' , Auth::user()->id)->first();

        return response()->json( $sms->no_of_sms  , 200 );
    }
    /* method to list the sms bundle cost chart */
    public function getSmsCostChart()
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
    /* method to get the sms cost per bundle */
    public function getSmsCost($id)
    {
        $sms = SmsPlan::where('id' , $id)->first();
        return response()->json( $sms->cost  , 200 );
    }
}
