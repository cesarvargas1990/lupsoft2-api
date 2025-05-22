<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePsempresaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {



        Schema::create('psempresa', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 60)->nullable();
            $table->string('nitempresa', 18)->nullable();
            $table->string('ddirec', 60)->nullable();
            $table->string('ciudad', 40)->nullable();
            $table->string('telefono', 60)->nullable();
            $table->string('pagina', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->double('vlr_capinicial', 10, 3)->nullable();
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
        Schema::dropIfExists('psempresa');
    }
}
