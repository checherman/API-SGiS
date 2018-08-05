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
			$table->foreign('altas_incidencias_id', 'multimedias_ibfk_1')->references('id')->on('altas_incidencias')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('referencias_id', 'multimedias_ibfk_2')->references('id')->on('referencias')->onUpdate('NO ACTION')->onDelete('NO ACTION');
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
			$table->dropForeign('multimedias_ibfk_1');
			$table->dropForeign('multimedias_ibfk_2');
		});
	}

}
