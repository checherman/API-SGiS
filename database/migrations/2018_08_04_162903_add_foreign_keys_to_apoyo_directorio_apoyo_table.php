<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToApoyoDirectorioApoyoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('apoyo_directorio_apoyo', function(Blueprint $table)
		{
			$table->foreign('apoyos_id', 'apoyo_directorio_apoyo_ibfk_1')->references('id')->on('apoyos')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('directorio_apoyos_id', 'apoyo_directorio_apoyo_ibfk_2')->references('id')->on('directorio_apoyos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('apoyo_directorio_apoyo', function(Blueprint $table)
		{
			$table->dropForeign('apoyo_directorio_apoyo_ibfk_1');
			$table->dropForeign('apoyo_directorio_apoyo_ibfk_2');
		});
	}

}
