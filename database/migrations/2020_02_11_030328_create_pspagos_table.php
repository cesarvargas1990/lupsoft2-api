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
			$table->date('fecha_realpago')->nullable();
			$table->integer('id_usureg')->nullable();
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
