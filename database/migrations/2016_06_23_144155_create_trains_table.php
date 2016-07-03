<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trains',function(Blueprint $table){
            $table->increments('id');
            $table->string('title');
            $table->integer('uid');
            $table->string('time')->comment('培训时间');
            $table->decimal('price');
            $table->integer('views');
            $table->smallInteger('vip')->comment('是否vip. 0:免费 1:收费');
            $table->string('image')->comment('缩略图');
            $table->string('content')->comment('培训内容');
            $table->string('webcastid')->comment('展示互动直播id');

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
        Schema::drop('trains');
    }
}
