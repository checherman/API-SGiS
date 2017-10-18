<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaSisLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_logs', function (Blueprint $table) {

            $table->increments('id')->unsigned();
            $table->integer('sis_usuarios_id')->unsigned();

            $table->string('clues_id');

            $table->string('ip');
            $table->string('mac');
            $table->string('tipo');
            $table->string('ruta');
            $table->string('controlador');
            $table->string('tabla');
            $table->text('peticion');
            $table->text('respuesta');
            $table->text('info');

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
        Schema::dropIfExists('sis_logs');
    }
}
