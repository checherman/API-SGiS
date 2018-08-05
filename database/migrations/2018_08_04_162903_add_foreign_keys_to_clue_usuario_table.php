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
			$table->foreign('sis_usuarios_id', 'clue_usuario_ibfk_1')->references('id')->on('sis_usuarios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
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
			$table->dropForeign('clue_usuario_ibfk_1');
		});
	}

}
