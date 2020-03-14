<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use WantedSasa\SmsPlan;

class SmsPlanTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sms_plans')->delete();

        $sms = new SmsPlan();
        $sms->no_of_sms = 30;
        $sms->cost = 1000;
        $sms->save();

        $sms = new SmsPlan();
        $sms->no_of_sms = 50;
        $sms->cost = 2000;
        $sms->save();

        $sms = new SmsPlan();
        $sms->no_of_sms = 60;
        $sms->cost = 3000;
        $sms->save();

        $sms = new SmsPlan();
        $sms->no_of_sms = 80;
        $sms->cost = 4000;
        $sms->save();

        $sms = new SmsPlan();
        $sms->no_of_sms = 100;
        $sms->cost = 5000;
        $sms->save();


    }
}
