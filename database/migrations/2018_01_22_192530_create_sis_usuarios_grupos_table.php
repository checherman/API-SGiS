<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSisUsuariosGruposTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sis_usuarios_grupos', function(Blueprint $table)
		{
			$table->integer('sis_usuarios_id')->unsigned()->index('fk_sis_usuarios_grupos_sis_usuarios1_idx');
			$table->integer('sis_grupos_id')->index('fk_sis_usuarios_grupos_sis_grupos1_idx');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sis_usuarios_grupos');
	}

}
