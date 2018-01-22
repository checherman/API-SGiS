<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAcompaniantePacienteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('acompaniante_paciente', function(Blueprint $table)
		{
			$table->foreign('acompaniantes_id', 'fk_acompaniante_paciente_acompaniantes_id')->references('id')->on('acompaniantes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('pacientes_id', 'fk_acompaniante_paciente_pacientes_id')->references('id')->on('pacientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('acompaniante_paciente', function(Blueprint $table)
		{
			$table->dropForeign('fk_acompaniante_paciente_acompaniantes_id');
			$table->dropForeign('fk_acompaniante_paciente_pacientes_id');
		});
	}

}
