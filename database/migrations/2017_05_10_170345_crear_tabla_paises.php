<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaPaises extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paises', function (Blueprint $table) {

            $table->increments('id')->unsigned();

            $table->string('nombre');
            $table->string('clave_ISOA2');
            $table->string('clave_A3');
            $table->string('clave_N3');
            $table->string('prefijo_telefono');

            $table->integer('creado_por');
            $table->integer('modificado_por');
            $table->integer('borrado_por');

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
        Schema::dropIfExists('paises');
    }
}
