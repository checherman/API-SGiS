<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToIncidenciaPacienteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('incidencia_paciente', function(Blueprint $table)
		{
			$table->foreign('incidencias_id', 'fk_incidencia_paciente_incidencias_id')->references('id')->on('incidencias')->onUpdate('CASCADE')->onDelete('NO ACTION');
			$table->foreign('pacientes_id', 'fk_incidencia_paciente_pacientes_id')->references('id')->on('pacientes')->onUpdate('CASCADE')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('incidencia_paciente', function(Blueprint $table)
		{
			$table->dropForeign('fk_incidencia_paciente_incidencias_id');
			$table->dropForeign('fk_incidencia_paciente_pacientes_id');
		});
	}

}
