<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use WantedSasa\WantedSasa;
use WantedSasa\sms;
use WantedSasa\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('users')->delete();


        $user = new User();
        $user->user_no = WantedSasa::generateAccountN0();
        $user->name = 'name';
        $user->email = 'email';
        $user->mobile_no = '256783159235';
        $user->password = bcrypt('keepkeep');
        $user->confirmation_code = WantedSasa::generatePIN(5);
        $user->country_code = substr( $user->mobile_no ,0,3 );
        $user->ip = '127.0.0.1';
        $user->save();

        $sms = new Sms();
        $sms->user_id = $user->id;
        $sms->no_of_sms = 5;
        $sms->ip = '127.0.0.1';
        $sms->save();

        $user->makeUser('Client');

    }
}
