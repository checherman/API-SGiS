<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToVisitasPuerperalesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('visitas_puerperales', function(Blueprint $table)
		{
			$table->foreign('altas_incidencias_id', 'visitas_puerperales_ibfk_1')->references('id')->on('altas_incidencias')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('visitas_puerperales', function(Blueprint $table)
		{
			$table->dropForeign('visitas_puerperales_ibfk_1');
		});
	}

}
