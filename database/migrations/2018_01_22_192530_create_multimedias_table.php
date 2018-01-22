<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMultimediasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('multimedias', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('referencias_id')->unsigned()->nullable()->index('multimedias_referencias_id_foreign');
			$table->integer('altas_incidencias_id')->unsigned()->nullable()->index('multimedias_altas_incidencias_id_foreign');
			$table->string('tipo', 191);
			$table->string('url', 191);
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
		Schema::drop('multimedias');
	}

}
