<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateApoyoDirectorioApoyoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('apoyo_directorio_apoyo', function(Blueprint $table)
		{
			$table->integer('apoyos_id')->unsigned()->index('apoyo_directorio_apoyo_apoyos_id_foreign');
			$table->integer('directorio_apoyos_id')->unsigned()->index('apoyo_directorio_apoyo_directorio_apoyos_id_foreign');
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('apoyo_directorio_apoyo');
	}

}
