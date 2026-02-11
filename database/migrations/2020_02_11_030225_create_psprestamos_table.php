<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePsprestamosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('psprestamos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_cliente');
            $table->foreign('id_cliente')->references('id')->on('psclientes');
            $table->unsignedInteger('id_tipo_sistema_prest');
            $table->foreign('id_tipo_sistema_prest')->references('id')->on('pstiposistemaprest');
            $table->double('valorpres', 13, 2)->nullable();
            $table->integer('numcuotas')->nullable();
            $table->unsignedInteger('id_periodo_pago')->nullable();
            $table->double('valcuota', 13, 2)->nullable();
            $table->double('porcint', 10, 2)->nullable();
            $table->date('fec_inicial')->nullable();
            $table->unsignedInteger('id_cobrador');
            $table->foreign('id_cobrador')->references('id')->on('users');
            $table->unsignedInteger('id_usureg');
            $table->foreign('id_usureg')->references('id')->on('users');
            $table->integer('ind_estado')->nullable();
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
        Schema::dropIfExists('psprestamos');
    }
}
