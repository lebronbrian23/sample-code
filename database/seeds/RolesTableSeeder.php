<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use WantedSasa\role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->delete();

        $role = new Role();
        $role->name = 'Admin';
        $role->save();

        $role = new Role();
        $role->name = 'Client';
        $role->save();
    }
}
