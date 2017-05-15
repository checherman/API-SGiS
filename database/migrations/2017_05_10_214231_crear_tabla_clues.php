<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaClues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clues', function (Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->string('clues')->unique();
            $table->string('nombre', 255);
            $table->string('domicilio', 255);
            $table->integer('codigoPostal');
            $table->double('numeroLongitud');
            $table->double('numeroLatitud');
            $table->string('entidad', 255);
            $table->string('estado', 255);
            $table->string('institucion', 255);
            $table->string('jurisdiccion', 255);
            $table->string('localidad', 255);
            $table->string('municipio', 255);
            $table->string('tipologia', 255);
            $table->integer('nivel_cone_id')->unsigned()->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->primary('clues');

            $table->foreign('nivel_cone_id')->references('id')->on('niveles_cones')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clues');
    }
}
