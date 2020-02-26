<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePsformapagoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        Schema::create('psformapago', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_periodo_pago');
            $table->double('valseguro',13,2)->nullable();
            $table->double('porcint',13,2)->nullable();
            $table->integer('ind_solicseguro')->nullable();
            $table->integer('ind_solicporcint')->nullable();
            $table->integer('ind_solinumc')->nullable();
            $table->integer('ind_solivalorpres')->nullable();
            $table->double('valorpres',13,2)->nullable();
            $table->integer('numcuotas')->nullable();
            $table->string('nomfpago');
            $table->string('nitempresa',30);
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
        Schema::dropIfExists('psformapago');
    }
}
