<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions',function(Blueprint $table){
            $table->increments('id');
            $table->integer('uid');
            $table->string('content');
            $table->string('object_type')->comment('问题的类型, train  strategy');
            $table->integer('object_id')->comment('问题的主体id');
            $table->integer('answers')->comment('回答的数量');
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
        Schema::drop('questions');
    }
}
