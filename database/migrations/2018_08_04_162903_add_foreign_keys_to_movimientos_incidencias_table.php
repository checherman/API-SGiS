<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMovimientosIncidenciasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('movimientos_incidencias', function(Blueprint $table)
		{
			$table->foreign('estados_pacientes_id', 'movimientos_incidencias_ibfk_1')->references('id')->on('estados_pacientes')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('incidencias_id', 'movimientos_incidencias_ibfk_2')->references('id')->on('incidencias')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('subcategorias_cie10_id', 'movimientos_incidencias_ibfk_3')->references('id')->on('subcategorias_cie10')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('triage_colores_id', 'movimientos_incidencias_ibfk_4')->references('id')->on('triage_colores')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('turnos_id', 'movimientos_incidencias_ibfk_5')->references('id')->on('turnos')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('ubicaciones_pacientes_id', 'movimientos_incidencias_ibfk_6')->references('id')->on('ubicaciones_pacientes')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('movimientos_incidencias', function(Blueprint $table)
		{
			$table->dropForeign('movimientos_incidencias_ibfk_1');
			$table->dropForeign('movimientos_incidencias_ibfk_2');
			$table->dropForeign('movimientos_incidencias_ibfk_3');
			$table->dropForeign('movimientos_incidencias_ibfk_4');
			$table->dropForeign('movimientos_incidencias_ibfk_5');
			$table->dropForeign('movimientos_incidencias_ibfk_6');
		});
	}

}
