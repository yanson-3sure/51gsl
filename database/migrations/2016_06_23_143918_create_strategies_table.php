<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStrategiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('strategies',function(Blueprint $table){
            $table->increments('id')->comment('策略');
            $table->string('title');
            $table->text('intro')->comment('简介');
            $table->integer('views')->default(0)->comment('点击/查看次数');
            $table->smallInteger('vip')->comment('是否收费  0:免费 1:收费');
            $table->text('content')->comment('策略内容');
            $table->string('risk')->comment('风险提示');
            $table->integer('uid');
            $table->timestamps();
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('strategies');
    }
}
