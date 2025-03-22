<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePsusperfilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		

        Schema::create('psusperfil', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_user');
			$table->unsignedInteger('id_perfil');
            $table->foreign('id_perfil')->references('id')->on('psperfil');
			$table->integer('ind_activo');
			$table->unsignedInteger('id_empresa');
            $table->foreign('id_empresa')->references('id')->on('psempresa');
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
        Schema::dropIfExists('psusperfil');
    }
}
