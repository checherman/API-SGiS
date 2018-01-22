<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMovimientosIncidenciasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('movimientos_incidencias', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('incidencias_id', 50)->index('movimientos_incidencias_incidencias_id_idx');
			$table->string('medico_reporta_id', 191)->nullable();
			$table->string('indicaciones', 191)->nullable();
			$table->string('reporte_medico', 191)->nullable();
			$table->integer('estados_pacientes_id')->unsigned()->nullable()->index('movimientos_incidencias_valoraciones_pacientes_id_foreign');
			$table->integer('ubicaciones_pacientes_id')->unsigned()->nullable()->index('movimientos_incidencias_estados_pacientes_id_foreign');
			$table->integer('triage_colores_id')->unsigned()->nullable()->index('movimientos_incidencias_triage_colores_id_foreign');
			$table->integer('subcategorias_cie10_id')->unsigned()->index('movimientos_incidencias_subcategorias_cie10_id_foreign');
			$table->integer('turnos_id')->unsigned()->nullable()->index('movimientos_incidencias_turnos_id_foreign');
			$table->timestamps();
			$table->softDeletes();
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
