<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaDiasFestivos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dias_festivos', function (Blueprint $table)
        {
            $table->engine = 'InnoDB';


            $table->integer('turno_id')->unsigned()->nullable();
            $table->foreign('turno_id')->references('id')->on('turnos');
            $table->string('nombre');
            $table->date('fecha');

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
        Schema::drop('dias_festivos');
    }
}
