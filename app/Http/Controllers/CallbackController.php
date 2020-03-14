<?php

namespace SampleProject\Http\Controllers;

use Illuminate\Http\Request;
use SampleProject\sms;
use SampleProject\Transaction;

class CallbackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
         $response = $request;

            $response_elements = explode( '{"hook', $response );

            $last_element = ( object ) json_decode( '{"hook'.$response_elements[ count( $response_elements ) -1 ] );

            if($last_element->hook->event == 'collection.received'){
                Transaction::where('transaction_id_from_callback'  ,$last_element->data->collection_request->id)
                    ->update([
                        'status' => $last_element->data->collection_request->status ,
                        'reference_id_from_callback' => $last_element->data->remote_transaction_id
                    ]);
                $transaction = Transaction::where('transaction_id_from_callback' ,$last_element->data->collection_request->id )->first();
                if ($transaction->status === 'processed'){
                    Sms::where('user_id',$last_element->data->collection_request->metadata->user_id)
                        ->update([
                            'no_of_sms' => $last_element->data->collection_request->metadata->no_of_sms,
                            'ip' => $last_element->data->collection_request->metadata->ip
                        ]);

                }

            }

            return  response('success', 200 );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    /*testing method*/
    public function create()
    {
        try{
            $response = 'POST /callback/response HTTP/1.1
Accept:                 */*
Accept-Encoding:        gzip, deflate
Connection:             keep-alive
Content-Length:         778
Content-Type:           application/json
Host:                   e.patasente.com
User-Agent:             python-requests/2.12.4
X-Newrelic-Id:          VQcAWF9XCBADXFdVAQkHUQ==
X-Newrelic-Transaction: PxQOWVdbDlAAV1ZSA1MDVQAHFB8EBw8RVU4aWwAJDARQVQpWAVVRVldSAUNKQVpRBFJZWlNQFTs=

{"hook": {"id": 85155, "event": "payment.status.changed", "target": "https://e.patasente.com/callback/response"}, "data": {"last_error": "", "currency": "UGX", "phone_nos": ["+256775526155"], "cancelled_time": null, "id": 283628, "updated_by": 1146, "author": 1146, "state": "processed", "start_date": "2017-05-04T18:45:41Z", "rejected_time": null, "description": "Withdrawal of UGX 1000 was done  byBakKa George", "rejected_reason": null, "account": 2874425, "cancelled_by": null, "created": "2017-05-04T18:45:41Z", "modified": "2017-05-04T18:46:00.818753Z", "rejected_by": null, "amount": "1000.0000", "payment_type": "money", "cancelled_reason": null, "organization": 483, "metadata": {"my_id": "2", "name": "invoice_payment" , "invoice_id": "1" , "generated_id": "IN31756" , "receiving_wallet": "1" , "paying_wallet": "2" , "to_user_id" :"2" , "from_user_id":"3"}, "remote_transaction_id": "3732108128"}, "event": 160435}';

            //  $response = $request;

            $response_elements = explode( '{"hook', $response );

            $last_element = ( object ) json_decode( '{"hook'.$response_elements[ count( $response_elements ) -1 ] );

            $patasente_wallet = Wallet::where('user_id', 18)->first();


            if($last_element->hook->event == 'collection.received'){

                $payment_method = TransferMethod::where('type','mobile deposits')->first();
                $charge =   $payment_method->charge  * $last_element->data->collection_request->amount;

                $wallet = User::find($last_element->data->collection_request->metadata->my_id)->wallet;

                $actual_deposit =  $last_element->data->collection_request->amount - $charge;

                Transaction::where('transaction_id_from_callback'  ,$last_element->data->collection_request->id)
                    ->update(
                        ['status' => $actual_deposit ]);

                Wallet::where( 'user_id' , $last_element->data->collection_request->metadata->my_id )
                    ->update([ 'amount' =>  $wallet->amount + $actual_deposit ]);

                Wallet::where('user_id', 18)
                    ->update(['amount' =>$patasente_wallet->amount + $payment_method->charge ]);

                $patasente_payment = new Payment();
                $patasente_payment->invoice_id = 0;
                $patasente_payment->to_wallet_id = $patasente_wallet->id;
                $patasente_payment->from_wallet_id =$wallet->id;
                $patasente_payment->paid_to = $patasente_wallet->user_id;
                $patasente_payment->paid_by = $last_element->data->collection_request->metadata->my_id;
                $patasente_payment->amount = $payment_method->charge;
                $patasente_payment->balance = 'charges';
                $patasente_payment->save();

            }
            else
            {
                if( $last_element->data->metadata->name == 'invoice_payment')
                {

                    $invoice = Invoice::where('id' , $last_element->data->metadata->invoice_id)->first();

                    $invoice_item = InvoiceItem::where('invoice_id', '=', $last_element->data->metadata->invoice_id)->where('deleted', 0)->get();

                    $payments = Payment::where('invoice_id', $last_element->data->metadata->invoice_id)->where('balance','!=', 'charges')->where('paid_to', $last_element->data->metadata->from_user_id)->where('paid_by', $last_element->data->metadata->to_user_id)->get();

                    $payment_method = TransferMethod::where('type','mobile withdraws')->first();
                    $charge =   $payment_method->charge;

                    if (!$payments) {

                        $total_payment = 0;
                        foreach ($invoice_item as $item) {

                            $amount = $item->amount;

                            $total_payment += $amount;
                        }

                        $grand_total =  ( ($invoice->vat / 100) * $total_payment + $total_payment) ;

                        $balance = $grand_total -  $last_element->data->amount;

                        $payment = new Payment();
                        $payment->invoice_id = $last_element->data->metadata->invoice_id;
                        $payment->to_wallet_id = $last_element->data->metadata->receiving_wallet;
                        $payment->from_wallet_id = $last_element->data->metadata->paying_wallet;
                        $payment->paid_to = $last_element->data->metadata->from_user_id;
                        $payment->paid_by = $last_element->data->metadata->to_user_id;
                        $payment->amount = $last_element->data->amount;
                        $payment->balance = $balance;
                        $payment->save();


                    } else {

                        $invoice = Invoice::where('id' , $last_element->data->metadata->invoice_id)->first();

                        $total_income = 0;
                        foreach ($invoice_item as $item) {

                            $amount = $item->amount;

                            $total_income += $amount;
                        }

                        $grand_total =  ( ($invoice->vat / 100) * $total_income + $total_income) ;

                        $paid = 0;
                        foreach ($payments as $item) {

                            $amount = $item->amount;

                            $paid += $amount;
                        }

                        $balances = $grand_total - $paid;

                        $total_item_balances = $balances - $last_element->data->amount;

                        $if_payment_exits = new Payment();
                        $if_payment_exits->invoice_id = $last_element->data->metadata->invoice_id;
                        $if_payment_exits->to_wallet_id = $last_element->data->metadata->receiving_wallet;
                        $if_payment_exits->from_wallet_id = $last_element->data->metadata->paying_wallet;
                        $if_payment_exits->paid_to = $last_element->data->metadata->from_user_id;
                        $if_payment_exits->paid_by = $last_element->data->metadata->to_user_id;
                        $if_payment_exits->amount = $last_element->data->amount;
                        $if_payment_exits->balance = $total_item_balances;
                        $if_payment_exits->save();

                    }

                    $wallet2 = User::find($last_element->data->metadata->my_id)->wallet;

                    Transaction::where('transaction_id_from_callback'  ,$last_element->data->id)
                        ->update(
                            ['status' => $last_element->data->state]
                        );

                    Wallet::where( 'user_id' , $last_element->data->metadata->my_id )
                        ->update([ 'amount' =>  $wallet2->amount - $last_element->data->amount - $charge->charge]);

                    Wallet::where('user_id', 18)
                        ->update(['amount' =>$patasente_wallet->amount + $payment_method->charge ]);

                    $patasente_payment = new Payment();
                    $patasente_payment->invoice_id = $last_element->data->metadata->invoice_id;
                    $patasente_payment->to_wallet_id = $patasente_wallet->id;
                    $patasente_payment->from_wallet_id = $wallet2->id;
                    $patasente_payment->paid_to = $patasente_wallet->user_id;
                    $patasente_payment->paid_by = $last_element->data->metadata->my_id;
                    $patasente_payment->amount = $payment_method->charge;
                    $patasente_payment->balance = 'charges';
                    $patasente_payment->save();

                }
                else
                {

                    $wallet2 = User::find($last_element->data->metadata->my_id)->wallet;
                    $payment_method = TransferMethod::where('type','mobile withdraws')->first();
                    $charge =   $payment_method->charge;

                    Transaction::where('transaction_id_from_callback'  ,$last_element->data->id)
                        ->update(
                            ['status' => $last_element->data->state]
                        );

                    Wallet::where( 'user_id' , $last_element->data->metadata->my_id )
                        ->update([ 'amount' =>  $wallet2->amount - $last_element->data->amount - $charge->charge]);

                    Wallet::where('user_id', 18)
                        ->update(['amount' =>$patasente_wallet->amount + $payment_method->charge ]);

                    $patasente_payment = new Payment();
                    $patasente_payment->invoice_id = 0;
                    $patasente_payment->to_wallet_id = $patasente_wallet->id;
                    $patasente_payment->from_wallet_id = $wallet2->id;
                    $patasente_payment->paid_to = $patasente_wallet->user_id;
                    $patasente_payment->paid_by = $last_element->data->metadata->my_id;
                    $patasente_payment->amount = $payment_method->charge;
                    $patasente_payment->balance = 'charges';
                    $patasente_payment->save();
                }
            }

            return  response('success', 200 );
        }catch (\Exception $e){

        }
    }

    public function doConfirmEmail(Request $request)
    {

        $user = User::where('confirmation_code', $request->get('email'))->first();

        if (!$user) return redirect('/login')->with('error', 'You need a verification code');

        if ($user->confirmation_code == $request->get('email') && $user->confirmed == 1) {

            return redirect('/login')->with('success', 'Your  email is already verified');

        } else {
            User::where('id', $user->id)
                ->where('confirmation_code', $request->get('email'))
                ->update(['confirmed' => 1]);

            return redirect('/login')->with('success', 'You have successfully verified your email');

        }
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
