<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',20)->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('mobile',11)->unique()->nullable();
            $table->string('password', 60)->nullable();
            $table->integer('role')->default(0);//0：普通用户 1：老师  -1：冻结用户
            $table->string('avatar',200)->nullable();
            $table->rememberToken();
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
        Schema::drop('users');
    }
}
