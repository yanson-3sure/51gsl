<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages',function(Blueprint $table){
            $table->increments('id');
            $table->text('message');
            $table->integer('event_id'); //status_id comment_id
            $table->string('event_type',20);//事件类型 praise:status(默认赞的status) praise:other comment
            $table->integer('from_uid');
            $table->integer('to_uid')->index();
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
        Schema::drop('messages');
    }
}
