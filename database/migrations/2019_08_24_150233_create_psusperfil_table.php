<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePsusperfilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		

        Schema::create('psusperfil', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_user');
			$table->string('id_perfil');
			$table->integer('ind_activo');
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
        Schema::dropIfExists('psusperfil');
    }
}
