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
            $table->integer('id_cliente')->nullable();
            $table->integer('id_prestamo')->nullable();
			$table->double('valcuota', 13,2)->nullable();
            $table->date('fecha_realpago')->nullable(); // fecha en que se realiza el pago
            $table->date('fecha_pago')->nullable(); // fecha que corresponde al pago (la fecha en la que debio haberse pagado)
            $table->integer('id_usureg')->nullable();
            $table->integer('id_fecha_pago')->nullable();
            $table->string('nitempresa',30)->nullable();
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
