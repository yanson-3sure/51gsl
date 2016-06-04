<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnalystsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('analysts',function(Blueprint $table){
            $table->integer('uid');
            $table->tinyInteger('status')->default(1)->comment('状态 0：停止使用  1：通过，正在使用');
            $table->string('role_name',20);//华讯金牌分析师
            $table->string('feature',1000)->comment('个人特色');
            $table->timestamp('audit_at'); ///审核 时间  用户申请后，通过审核时间
            $table->integer('application_id')->default(0); //申请id   预留 以后在线申请
            $table->timestamps();
            $table->primary('uid');
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
        Schema::drop('analysts');
    }
}
