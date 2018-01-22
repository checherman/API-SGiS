<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMultimediasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('multimedias', function(Blueprint $table)
		{
			$table->foreign('altas_incidencias_id', 'fk_multimedias_altas_incidencias_id')->references('id')->on('altas_incidencias')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('referencias_id', 'fk_multimedias_referencias_id')->references('id')->on('referencias')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('multimedias', function(Blueprint $table)
		{
			$table->dropForeign('fk_multimedias_altas_incidencias_id');
			$table->dropForeign('fk_multimedias_referencias_id');
		});
	}

}
