<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAcompaniantePacienteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('acompaniante_paciente', function(Blueprint $table)
		{
			$table->integer('pacientes_id')->unsigned()->nullable()->index('acompaniante_paciente_pacientes_id_foreign');
			$table->integer('acompaniantes_id')->unsigned()->nullable()->index('acompaniante_paciente_acompaniantes_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('acompaniante_paciente');
	}

}
