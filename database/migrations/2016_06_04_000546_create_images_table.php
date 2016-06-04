<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('images', function (Blueprint $table) {
            $table->increments('id');
            $table->string('path');
            $table->string('ext',10);
            $table->string('url');
            $table->integer('uid')->index();
            $table->tinyInteger('valid')->default(1); //状态 1:可用 默认1
            $table->string('type',20)->comment('图片类型 status strategy train');
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
        //
        Schema::drop('images');
    }
}
