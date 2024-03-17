<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prepayments', function (Blueprint $table) {
            $table->id();
            $table->integer('webinar_id')->unsigned()->nullable();
            $table->foreign('webinar_id')->references('id')->on('webinars')->onDelete('cascade');
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('amount')->unsigned();
            $table->integer('pay')->unsigned()->nullable();
            $table->enum('status', ['pending', 'done', 'refund_request', 'refunded']);
            $table->integer('order_id')->unsigned()->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->timestamp('refund_at')->nullable();
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
        Schema::dropIfExists('prepayments');
    }
};
