<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToIncidenciaClueTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('incidencia_clue', function(Blueprint $table)
		{
			$table->foreign('incidencias_id', 'incidencia_clue_ibfk_1')->references('id')->on('incidencias')->onUpdate('CASCADE')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('incidencia_clue', function(Blueprint $table)
		{
			$table->dropForeign('incidencia_clue_ibfk_1');
		});
	}

}
