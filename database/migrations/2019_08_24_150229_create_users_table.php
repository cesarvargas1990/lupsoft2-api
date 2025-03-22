<?php

use Illuminate\Support\Facades\Schema;
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
            $table->string('name');
            $table->string('email')->unique()->notNullable();
            $table->string('password');
			$table->unsignedInteger('id_empresa')->nullable();
            $table->foreign('id_empresa')->references('id')->on('psempresa');
			$table->integer('is_admin');
			$table->unsignedInteger('id_user')->nullable();
            $table->foreign('id_user')->references('id')->on('users');
			$table->integer('ind_activo');
		
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
        Schema::dropIfExists('users');
    }
}
