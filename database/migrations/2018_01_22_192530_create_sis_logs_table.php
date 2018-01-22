<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSisLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sis_logs', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('sis_usuarios_id')->unsigned()->index('fk_sis_logs_sis_usuarios1_idx');
			$table->integer('clues_id');
			$table->string('ip', 19);
			$table->string('mac', 19);
			$table->string('tipo', 6);
			$table->string('ruta', 50);
			$table->string('controlador', 45);
			$table->string('tabla', 25);
			$table->text('peticion', 65535);
			$table->text('respuesta', 65535);
			$table->text('info', 65535)->nullable();
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
		Schema::drop('sis_logs');
	}

}
