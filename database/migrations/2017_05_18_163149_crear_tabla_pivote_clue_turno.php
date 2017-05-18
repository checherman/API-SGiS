<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaPivoteClueTurno extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clue_turno', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->string('clues_id');
            $table->integer('turno_id')->unsigned();

            $table->foreign('clues_id')
                ->references('clues')->on('clues')
                ->onDelete('cascade');

            $table->foreign('turno_id')
                ->references('id')->on('turnos')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('clue_turno');
    }
}
