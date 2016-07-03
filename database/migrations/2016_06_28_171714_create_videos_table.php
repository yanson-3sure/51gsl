<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        +"attendeeJoinUrl": "http://hxbj.gensee.com/webcast/site/vod/play-1dd7872a0ac4421a982ff48817615dea"
//    +"convertResult": 1
//    +"createdTime": 1467025355000
//    +"creator": 25811562
//    +"description": ""
//    +"grType": 0
//    +"id": "1dd7872a0ac4421a982ff48817615dea"
//    +"number": "98894837"
//    +"password": ""
//    +"recordEndTime": 1467025353000
//    +"recordId": 5028822
//    +"recordStartTime": 1467021503000
//    +"subject": "18-19点培训课"
//    +"webcastId": "63aacb3be0f4467198d7908970409fb5"
        Schema::create('videos', function (Blueprint $table) {
            $table->string('id')->comment('展示互动历史点播列表');
            $table->string('attendeeJoinUrl');
            $table->smallInteger('convertResult');
            $table->timestamp('createdTime')->index();
            $table->integer('creator');
            $table->string('description');
            $table->smallInteger('grType');
            $table->integer('number');
            $table->string('password');
            $table->timestamp('recordEndTime');
            $table->integer('recordId');
            $table->timestamp('recordStartTime');
            $table->string('subject');
            $table->string('webcastId')->index();
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
        Schema::drop('videos');
    }
}
