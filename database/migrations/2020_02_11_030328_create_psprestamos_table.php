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
			$table->double('valorpres', 13,2)->nullable();
			$table->integer('numcuotas')->nullable();
			$table->integer('codfpago')->nullable();
			$table->double('valseguro', 13,2)->nullable();
			$table->double('valcuota', 13,2)->nullable();
			$table->double('porcint', 10,2)->nullable();
			$table->date('fec_inicial')->nullable();
			$table->integer('id_cobrador')->nullable();
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
