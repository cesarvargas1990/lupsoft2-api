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
            $table->unsignedInteger('id_tipo_docid');
            $table->foreign('id_tipo_docid')->references('id')->on('pstipodocidenti');
            $table->string('numdocumento', 30)->nullable();
            $table->string('ciudad', 50)->nullable();
            $table->string('telefijo', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('direcasa', 200)->nullable();
            $table->string('diretrabajo', 200)->nullable();
            $table->string('ubicasa', 255)->nullable();
            $table->string('ubictrabajo', 255)->nullable();
            $table->unsignedInteger('id_empresa')->nullable();
            $table->foreign('id_empresa')->references('id')->on('psempresa');
            $table->string('ref1', 255)->nullable();
            $table->string('ref2', 255)->nullable();
            $table->unsignedInteger('id_cobrador');
            $table->foreign('id_cobrador')->references('id')->on('users');
            $table->string('email', 255)->nullable();
            $table->date('fch_expdocumento')->nullable();
            $table->date('fch_nacimiento')->nullable();
            $table->unsignedInteger('id_user')->nullable();
            $table->foreign('id_user')->references('id')->on('users');
            $table->integer('ind_estado');
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
