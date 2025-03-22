<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePsdocadjuntosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        Schema::create('psdocadjuntos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('rutaadjunto',100);
            $table->integer('id_tdocadjunto')->nullable();
            $table->unsignedInteger('id_usu_cargarch');
            $table->foreign('id_usu_cargarch')->references('id')->on('users');
            $table->unsignedInteger('id_cliente')->nullable();
            $table->foreign('id_cliente')->references('id')->on('psclientes');
            $table->string('nombrearchivo',1000);
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
        Schema::dropIfExists('psdocadjuntos');
    }
}
