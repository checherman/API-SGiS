<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIncidenciaClueTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('incidencia_clue', function(Blueprint $table)
		{
			$table->string('incidencias_id', 50)->index('incidencia_clue_incidencias_id_foreign');
			$table->string('clues', 12)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('incidencia_clue');
	}

}
