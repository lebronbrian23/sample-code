<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_no');
            $table->integer('user_id')->unsigned();
            $table->string('name');
            $table->string('description');
            $table->string('location');
            $table->string('price');
            $table->longText('picture');
            $table->string('type');
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('deleted')->default(0);
            $table->string('ip');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
