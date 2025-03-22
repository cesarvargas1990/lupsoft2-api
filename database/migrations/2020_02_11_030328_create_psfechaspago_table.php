<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePsfechaspagoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('psfechaspago', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_prestamo');
			$table->double('valor_cuota',13,2);
			$table->double('valor_pagar',13,2);
            $table->date('fecha_pago');
            $table->integer('ind_renovar');
            $table->integer('ind_estado');
            $table->unsignedInteger('id_cliente')->nullable();
            $table->foreign('id_cliente')->references('id')->on('psclientes');
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
        Schema::dropIfExists('psfechaspago');
    }
}
