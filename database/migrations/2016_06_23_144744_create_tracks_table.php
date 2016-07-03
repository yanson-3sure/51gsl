<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //策略跟踪
        Schema::create('tracks',function(Blueprint $table){
            $table->increments('id');
            $table->integer('strategy_id');
            $table->string('status')->comment('关注 买入 离场');
            $table->text('content');
            $table->string('image')->comment('缩略图');
            $table->integer('uid');
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
        Schema::drop('tracks');
    }
}
