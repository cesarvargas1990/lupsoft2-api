<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerfilAccionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('psperfilaccion', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_perfil');
            $table->foreign('id_perfil')->references('id')->on('users');
            $table->string('nom_accion');
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
        Schema::dropIfExists('psperfilaccion');
    }
}
