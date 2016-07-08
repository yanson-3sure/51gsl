<?php

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
        //
        Schema::create('orders',function(Blueprint $table){
            $table->string('id',191);
            $table->integer('uid');
            $table->string('mobile')->comment('下订单时的手机号');
            $table->integer('product_id');
            $table->string('product_type',20)->comment('产品类型 analyst:分析师');
            $table->integer('number')->default(1)->comment('数量');
            $table->decimal('price');
            $table->decimal('price_pay');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('start_at');
            $table->timestamp('end_at');
            $table->smallInteger('order_type')->comment('订单类型 1.试用 2.新单 3.续费 11.退费');
            $table->string('status')->index()->comment('支付状态 paying paid paid_faild');
            $table->string('center_order_id')->comment('订单中心,订单编号');
            $table->string('transaction_id')->comment('微信支付id');
            $table->timestamps();
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
