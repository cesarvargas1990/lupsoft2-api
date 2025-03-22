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
            $table->integer('id_usu_cargarch')->nullable();
            $table->integer('id_cliente')->nullable();
            $table->string('nombrearchivo',1000);
            $table->string('id_empresa',30);
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
