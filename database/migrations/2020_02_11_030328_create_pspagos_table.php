<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePspagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pspagos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_cliente')->nullable();
            $table->foreign('id_cliente')->references('id')->on('psclientes');
            $table->unsignedInteger('id_prestamo')->nullable();
            $table->foreign('id_prestamo')->references('id')->on('psprestamos');
            $table->double('valcuota', 13, 2)->nullable();
            $table->date('fecha_realpago')->nullable(); // fecha en que se realiza el pago
            $table->date('fecha_pago')->nullable(); // fecha que corresponde al pago (la fecha en la que debio haberse pagado)
            $table->unsignedInteger('id_usureg');
            $table->foreign('id_usureg')->references('id')->on('users');
            $table->unsignedInteger('id_fecha_pago')->nullable();
            $table->foreign('id_fecha_pago')->references('id')->on('psfechaspago');
            $table->unsignedInteger('id_empresa');
            $table->foreign('id_empresa')->references('id')->on('psempresa');
            $table->integer('ind_estado')->nullable();
            $table->integer('ind_abonocapital')->nullable();
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
        Schema::dropIfExists('pspagos');
    }
}
