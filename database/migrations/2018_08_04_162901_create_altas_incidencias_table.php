<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAltasIncidenciasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('altas_incidencias', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('incidencias_id', 50)->nullable()->index('fk_altas_incidencias_incidencias_id_idx');
			$table->string('clues_contrarefiere', 191)->nullable();
			$table->string('clues_regresa', 191)->nullable();
			$table->string('medico_reporta_id', 191)->nullable();
			$table->integer('metodos_planificacion_id')->unsigned()->nullable()->index('altas_incidencias_metodos_planificacion_id_foreign');
			$table->integer('tipos_altas_id')->unsigned()->nullable()->index('altas_incidencias_estados_pacientes_id_foreign');
			$table->integer('turnos_id')->unsigned()->nullable()->index('altas_incidencias_turnos_id_foreign');
			$table->string('resumen_clinico', 191)->nullable();
			$table->string('diagnostico_egreso', 191)->nullable();
			$table->string('observacion_trabajo_social', 191)->nullable();
			$table->string('instrucciones_recomendaciones', 191)->nullable();
			$table->timestamps();
			$table->softDeletes();
			$table->string('status', 45)->nullable()->default('Por visitar');
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
