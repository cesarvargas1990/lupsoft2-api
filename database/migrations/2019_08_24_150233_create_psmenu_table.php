<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePsmenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('psmenu', function (Blueprint $table) {
            $table->increments('id');
			$table->string('nombre',100)->nullable();
            $table->string('ruta',500)->nullable();
			$table->string('icono',50)->nullable();
            $table->integer('orden')->nullable();
			$table->integer('id_mpadre')->nullable();
			$table->integer('id_perfil')->nullable();
			$table->integer('ind_activo')->nullable();
			$table->string('id_empresa',30);
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
        Schema::dropIfExists('psmenu');
    }
}
