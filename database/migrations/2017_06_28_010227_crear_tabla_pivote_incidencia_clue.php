<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaPivoteIncidenciaClue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incidencia_clue', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->string('incidencias_id');
            $table->string('clues');

            $table->foreign('incidencias_id')
                ->references('id')->on('incidencias')
                ->onDelete('cascade');

//            $table->foreign('clues')
//                ->references('clues')->on('clues')
//                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('incidencia_clue');
    }
}
