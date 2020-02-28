<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('oid');
            $table->string('ordersn');
            $table->integer('uid')->default(0);
            $table->string('openid')->default('');
            $table->string('xm');
            $table->string('address');
            $table->string('mobile');
            $table->float('money',7,2);
            $table->tinyinteger('ispay')->default(0);
            $table->integer('ordertime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
