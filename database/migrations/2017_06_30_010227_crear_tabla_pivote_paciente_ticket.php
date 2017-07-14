<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaPivotePacienteTicket extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paciente_ticket', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->string('pacientes_id');
            $table->string('incidencias_id');
            $table->string('acompaniantes_id')->nullable();

            $table->foreign('pacientes_id')
                ->references('id')->on('pacientes')
                ->onDelete('cascade');

            $table->foreign('incidencias_id')
                ->references('id')->on('incidencias')
                ->onDelete('cascade');

            $table->foreign('acompaniantes_id')
                ->references('id')->on('acompaniantes')
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
        Schema::drop('paciente_ticket');
    }
}
