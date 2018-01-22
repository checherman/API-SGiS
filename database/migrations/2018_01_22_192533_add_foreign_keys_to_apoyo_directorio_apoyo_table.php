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
			$table->foreign('apoyos_id')->references('id')->on('apoyos')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('directorio_apoyos_id')->references('id')->on('directorio_apoyos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
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
			$table->dropForeign('apoyo_directorio_apoyo_apoyos_id_foreign');
			$table->dropForeign('apoyo_directorio_apoyo_directorio_apoyos_id_foreign');
		});
	}

}
