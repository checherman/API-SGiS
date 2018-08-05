<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClueUsuarioTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('clue_usuario', function(Blueprint $table)
		{
			$table->integer('sis_usuarios_id')->unsigned()->nullable()->index('fk_clue_usuario_id_idx');
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
		Schema::drop('clue_usuario');
	}

}
