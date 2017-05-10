<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCluesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clues', function (Blueprint $table) {

            $table->string('clues');
            $table->string('nombre', 255)->nullable()->default(null);
            $table->string('domicilio', 255)->nullable()->default(null);
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

            $table->timestamps();
            $table->softDeletes();
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
