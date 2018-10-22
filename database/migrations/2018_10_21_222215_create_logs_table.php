<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('log_sub_type_id');
            $table->foreign('log_sub_type_id')->references('id')->on('log_sub_types')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('user_id');
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('no action')->onUpdate('cascade');
            $table->string('information')->nullable();
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
        Schema::dropIfExists('logs');
    }
}
