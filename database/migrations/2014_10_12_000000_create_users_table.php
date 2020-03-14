<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_no');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('mobile_no');
            $table->string('password')->nullable();
            $table->string('country_code')->nullable();
            $table->string('address')->nullable();
            $table->longText('picture')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->tinyInteger('confirmed')->default(0);
            $table->integer('confirmation_code');
            $table->string('ip');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
