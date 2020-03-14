<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('transaction_id');
            $table->string('transaction_id_from_callback')->nullable();
            $table->string('reference_id_from_callback')->nullable();
            $table->integer('item_id')->unsigned()->nullable();
            $table->string('names');
            $table->string('msisdn')->nullable();
            $table->string('amount');
            $table->string('balance')->default(0);
            $table->string('charge')->default(0);
            $table->string('transaction_status')->nullable();
            $table->string('payment_reason')->nullable();
            $table->integer('paid_to')->unsigned()->nullable();
            $table->integer('paid_by')->unsigned()->nullable();
            $table->string('type');
            $table->tinyInteger('deleted')->default(0);
            $table->string('ip');
            $table->timestamps();
            $table->foreign('item_id')->references('id')->on('items');
            $table->foreign('paid_to')->references('id')->on('users');
            $table->foreign('paid_by')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
