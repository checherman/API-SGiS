<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaPivoteIncidenciaPaciente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incidencia_paciente', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->string('pacientes_id');
            $table->string('incidencias_id');

            $table->foreign('pacientes_id')
                ->references('id')->on('pacientes')
                ->onDelete('cascade');

            $table->foreign('incidencias_id')
                ->references('id')->on('incidencias')
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
        Schema::drop('incidencia_paciente');
    }
}
