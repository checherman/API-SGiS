<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAltasIncidenciasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('altas_incidencias', function(Blueprint $table)
		{
			$table->foreign('incidencias_id', 'fk_altas_incidencias_incidencias_id')->references('id')->on('incidencias')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('metodos_planificacion_id', 'fk_altas_incidencias_metodos_planificacion_id')->references('id')->on('metodos_planificacion')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('tipos_altas_id', 'fk_altas_incidencias_tipos_altas_id')->references('id')->on('tipos_altas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('turnos_id', 'fk_altas_incidencias_turnos_id')->references('id')->on('turnos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('altas_incidencias', function(Blueprint $table)
		{
			$table->dropForeign('fk_altas_incidencias_incidencias_id');
			$table->dropForeign('fk_altas_incidencias_metodos_planificacion_id');
			$table->dropForeign('fk_altas_incidencias_tipos_altas_id');
			$table->dropForeign('fk_altas_incidencias_turnos_id');
		});
	}

}
