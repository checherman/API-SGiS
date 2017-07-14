<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaMovimientosIncidencias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimientos_incidencias', function (Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->string('id');
            $table->string('servidor_id',4);
            $table->string('incidencias_id');
            $table->string('medico_reporta_id')->nullable();
            $table->string('indicaciones');
            $table->string('reporte_medico');
            $table->string('diagnostico_egreso')->nullable();
            $table->string('observacion_trabajo_social')->nullable();
            $table->integer('metodos_planificacion_id')->unsigned()->nullable();
            $table->integer('estados_incidencias_id')->unsigned();
            $table->integer('valoraciones_pacientes_id')->unsigned();
            $table->integer('estados_pacientes_id')->unsigned();

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');

            $table->foreign('metodos_planificacion_id')->references('id')->on('metodos_planificacion')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('estados_incidencias_id')->references('id')->on('estados_incidencias')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('valoraciones_pacientes_id')->references('id')->on('valoraciones_pacientes')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('estados_pacientes_id')->references('id')->on('estados_pacientes')
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
        Schema::drop('movimientos_incidencias');
    }
}
