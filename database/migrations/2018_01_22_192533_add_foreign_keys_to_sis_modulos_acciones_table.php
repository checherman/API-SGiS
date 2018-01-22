<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSisModulosAccionesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('sis_modulos_acciones', function(Blueprint $table)
		{
			$table->foreign('sis_modulos_id', 'fk_sis_modulos_acciones_sis_modulos1')->references('id')->on('sis_modulos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('sis_modulos_acciones', function(Blueprint $table)
		{
			$table->dropForeign('fk_sis_modulos_acciones_sis_modulos1');
		});
	}

}
