<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToClueUsuarioTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('clue_usuario', function(Blueprint $table)
		{
			$table->foreign('sis_usuarios_id', 'fk_clue_usuario_id')->references('id')->on('sis_usuarios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('clue_usuario', function(Blueprint $table)
		{
			$table->dropForeign('fk_clue_usuario_id');
		});
	}

}
