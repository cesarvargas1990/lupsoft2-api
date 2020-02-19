<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePsclientesTable extends Migration
{ 
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        Schema::create('psclientes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nomcliente')->nullable();
            $table->integer('codtipdocid')->nullable();
            $table->string('numdocumento',30)->nullable();
	    $table->string('ciudad',50)->nullable();
            $table->string('telefijo',20)->nullable();
            $table->string('celular',20)->nullable();
            $table->string('direcasa',200)->nullable();
            $table->string('diretrabajo',200)->nullable();
            $table->string('ubicasa',255)->nullable();
            $table->string('ubictrabajo',255)->nullable();
            $table->string('nitempresa',30);
	    $table->string('ref1',255)->nullable();
	    $table->string('ref2',255)->nullable();
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
        Schema::dropIfExists('psclientes');
    }
}
