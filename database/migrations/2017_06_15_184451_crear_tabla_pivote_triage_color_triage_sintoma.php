<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaPivoteTriageColorTriageSintoma extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('triage_color_triage_sintoma', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->string('nombre');
            $table->integer('triage_sintomas_id')->unsigned();
            $table->integer('triage_colores_id')->unsigned();

            $table->foreign('triage_sintomas_id')
                ->references('id')->on('triage_sintomas')
                ->onDelete('cascade');

            $table->foreign('triage_colores_id')
                ->references('id')->on('triage_colores')
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
        Schema::drop('triage_color_triage_sintoma');
    }
}
