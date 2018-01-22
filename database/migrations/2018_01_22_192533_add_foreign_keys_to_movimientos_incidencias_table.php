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
			$table->foreign('estados_pacientes_id')->references('id')->on('estados_pacientes')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('incidencias_id', 'movimientos_incidencias_incidencias_id')->references('id')->on('incidencias')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('subcategorias_cie10_id', 'movimientos_incidencias_subcategorias_cie10_id')->references('id')->on('subcategorias_cie10')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('triage_colores_id')->references('id')->on('triage_colores')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('turnos_id')->references('id')->on('turnos')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('ubicaciones_pacientes_id')->references('id')->on('ubicaciones_pacientes')->onUpdate('CASCADE')->onDelete('CASCADE');
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
			$table->dropForeign('movimientos_incidencias_estados_pacientes_id_foreign');
			$table->dropForeign('movimientos_incidencias_incidencias_id');
			$table->dropForeign('movimientos_incidencias_subcategorias_cie10_id');
			$table->dropForeign('movimientos_incidencias_triage_colores_id_foreign');
			$table->dropForeign('movimientos_incidencias_turnos_id_foreign');
			$table->dropForeign('movimientos_incidencias_ubicaciones_pacientes_id_foreign');
		});
	}

}
