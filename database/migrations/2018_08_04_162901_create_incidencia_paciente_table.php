<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIncidenciaPacienteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('incidencia_paciente', function(Blueprint $table)
		{
			$table->integer('pacientes_id')->unsigned()->index('incidencia_paciente_pacientes_id_foreign');
			$table->string('incidencias_id', 50)->index('incidencia_paciente_incidencias_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('incidencia_paciente');
	}

}
