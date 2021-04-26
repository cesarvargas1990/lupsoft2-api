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
            $table->integer('id_cliente')->nullable();
			$table->integer('codtipsistemap')->nullable();
			$table->double('valorpres', 13,2)->nullable();
			$table->integer('numcuotas')->nullable();
			$table->integer('id_forma_pago')->nullable();
			$table->double('valcuota', 13,2)->nullable();
			$table->double('porcint', 10,2)->nullable();
			$table->date('fec_inicial')->nullable();
			$table->integer('id_cobrador')->nullable();
            $table->integer('id_usureg')->nullable();
            $table->integer('ind_estado')->nullable();
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
        Schema::dropIfExists('psprestamos');
    }
}
