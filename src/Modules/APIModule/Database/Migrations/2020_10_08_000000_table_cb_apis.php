<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableCbApis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cb_apis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("name");
            $table->text("description");
            $table->string("http_method");
            $table->tinyInteger("token_guard")->default(1);
            $table->string("api_action");
            $table->string("api_path");
            $table->longText("api_params");
            $table->longText("api_responses");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cb_apis');
    }
}
