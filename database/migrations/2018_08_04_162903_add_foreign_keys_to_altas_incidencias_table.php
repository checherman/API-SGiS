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
			$table->foreign('incidencias_id', 'altas_incidencias_ibfk_1')->references('id')->on('incidencias')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('metodos_planificacion_id', 'altas_incidencias_ibfk_2')->references('id')->on('metodos_planificacion')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('tipos_altas_id', 'altas_incidencias_ibfk_3')->references('id')->on('tipos_altas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('turnos_id', 'altas_incidencias_ibfk_4')->references('id')->on('turnos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
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
			$table->dropForeign('altas_incidencias_ibfk_1');
			$table->dropForeign('altas_incidencias_ibfk_2');
			$table->dropForeign('altas_incidencias_ibfk_3');
			$table->dropForeign('altas_incidencias_ibfk_4');
		});
	}

}
