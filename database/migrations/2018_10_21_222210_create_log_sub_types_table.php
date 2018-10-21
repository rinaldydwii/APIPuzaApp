<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogSubTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_sub_types', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('log_type_id');
            $table->foreign('log_type_id')->references('id')->on('log_types')->onDelete('cascade')->onUpdate('cascade');
            $table->string('name');
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
        Schema::dropIfExists('log_sub_types');
    }
}
