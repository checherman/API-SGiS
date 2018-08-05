<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSisModulosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('sis_modulos', function(Blueprint $table)
		{
			$table->foreign('sis_modulos_id', 'sis_modulos_ibfk_1')->references('id')->on('sis_modulos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('sis_modulos', function(Blueprint $table)
		{
			$table->dropForeign('sis_modulos_ibfk_1');
		});
	}

}
