<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uid')->index();
            $table->text('message');
            $table->integer('image_id')->default(0);
            $table->integer('forward_id')->default(0);
            $table->string('forward_type')->comment('comment status strategy train');
            $table->smallInteger('isComment')->default(1)->comment('是否允许评论,0:否,1:是');
            $table->softDeletes();
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
        Schema::drop('statuses');
    }
}
