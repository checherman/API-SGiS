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
			$table->foreign('acompaniantes_id', 'acompaniante_paciente_ibfk_1')->references('id')->on('acompaniantes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('pacientes_id', 'acompaniante_paciente_ibfk_2')->references('id')->on('pacientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
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
			$table->dropForeign('acompaniante_paciente_ibfk_1');
			$table->dropForeign('acompaniante_paciente_ibfk_2');
		});
	}

}
