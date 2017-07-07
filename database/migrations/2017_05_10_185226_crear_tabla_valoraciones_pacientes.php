<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaValoracionesPacientes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('valoraciones_pacientes', function (Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('nombre');
            $table->string('descripcion');

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
        Schema::drop('valoraciones_pacientes');
    }
}
