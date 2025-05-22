<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePstdocplantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    // pstdocplant
    // Pstdocplant

    public function up()
    {
        Schema::create('pstdocplant', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
            $table->longText('plantilla_html');
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
        Schema::dropIfExists('pstdocplant');
    }
}
