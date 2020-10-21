<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableCbRolePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cb_role_permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId("cb_roles_id")->constrained("cb_roles");
            $table->foreignId("cb_modules_id")->constrained("cb_modules");
            $table->tinyInteger("can_browse")->default(1);
            $table->tinyInteger("can_create")->default(1);
            $table->tinyInteger("can_read")->default(1);
            $table->tinyInteger("can_update")->default(1);
            $table->tinyInteger("can_delete")->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cb_role_permissions');
    }
}
