<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications',function(Blueprint $table) {
            $table->increments('id');
            $table->integer('uid');
            $table->string('id_number',30)->comment('身份证号');
            $table->string('securities_certificate',30)->comment('证券从业资格编号');
            $table->string('role_name',20);//华讯金牌分析师
            $table->string('feature',1000)->comment('个人特色');

            $table->tinyInteger('status')->default(1)->comment('状态 0：申请  1：通过，正在使用 2:未通过');
            $table->timestamp('audit_at'); ///审核 时间  用户申请后，通过审核时间
            $table->string('audit_name',20);
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
        Schema::drop('applications');
    }
}
