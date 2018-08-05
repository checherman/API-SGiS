<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToIncidenciasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('incidencias', function(Blueprint $table)
		{
			$table->foreign('estados_incidencias_id', 'incidencias_ibfk_1')->references('id')->on('estados_incidencias')->onUpdate('CASCADE')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('incidencias', function(Blueprint $table)
		{
			$table->dropForeign('incidencias_ibfk_1');
		});
	}

}
