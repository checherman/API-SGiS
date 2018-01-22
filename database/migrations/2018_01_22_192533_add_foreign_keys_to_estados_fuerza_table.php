<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToEstadosFuerzaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('estados_fuerza', function(Blueprint $table)
		{
			$table->foreign('turnos_id', 'fk_estados_fuerza_turnos_id')->references('id')->on('turnos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('estados_fuerza', function(Blueprint $table)
		{
			$table->dropForeign('fk_estados_fuerza_turnos_id');
		});
	}

}
