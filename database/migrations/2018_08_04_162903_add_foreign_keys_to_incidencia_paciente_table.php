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
			$table->foreign('incidencias_id', 'incidencia_paciente_ibfk_1')->references('id')->on('incidencias')->onUpdate('CASCADE')->onDelete('NO ACTION');
			$table->foreign('pacientes_id', 'incidencia_paciente_ibfk_2')->references('id')->on('pacientes')->onUpdate('CASCADE')->onDelete('NO ACTION');
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
			$table->dropForeign('incidencia_paciente_ibfk_1');
			$table->dropForeign('incidencia_paciente_ibfk_2');
		});
	}

}
