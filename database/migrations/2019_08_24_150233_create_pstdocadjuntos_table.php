<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePstdocadjuntosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pstdocadjuntos', function (Blueprint $table) {
            $table->increments('id');
			$table->string('nombre',100)->nullable();
			$table->unsignedInteger('id_empresa');
            $table->foreign('id_empresa')->references('id')->on('psempresa');
			$table->integer('idtipaccionsist')->nullable();
			
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
        Schema::dropIfExists('pstdocadjuntos');
    }
}
