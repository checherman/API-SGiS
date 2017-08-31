<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaAltasIncidencias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('altas_incidencias', function (Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->string('id');
            $table->string('servidor_id',4);
            $table->integer('incremento');
            $table->string('incidencias_id');

            $table->string('medico_reporta_id')->nullable();
            $table->string('diagnostico_egreso')->nullable();
            $table->string('observacion_trabajo_social')->nullable();
            $table->integer('metodos_planificacion_id')->unsigned()->nullable();

            $table->integer('estados_pacientes_id')->unsigned()->nullable();
            $table->integer('turnos_id')->unsigned()->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');

            $table->foreign('metodos_planificacion_id')->references('id')->on('metodos_planificacion')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('estados_pacientes_id')->references('id')->on('estados_pacientes')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('turnos_id')->references('id')->on('turnos')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('altas_incidencias');
    }
}
