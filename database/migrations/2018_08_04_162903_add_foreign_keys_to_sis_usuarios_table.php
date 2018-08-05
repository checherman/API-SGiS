<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSisUsuariosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('sis_usuarios', function(Blueprint $table)
		{
			$table->foreign('cargos_id', 'sis_usuarios_ibfk_1')->references('id')->on('cargos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('sis_usuarios', function(Blueprint $table)
		{
			$table->dropForeign('sis_usuarios_ibfk_1');
		});
	}

}
