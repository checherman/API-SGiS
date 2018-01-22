<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSisUsuariosNotificacionesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sis_usuarios_notificaciones', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('sis_usuarios_id')->nullable();
			$table->integer('tipos_notificaciones_id')->nullable();
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
		Schema::drop('sis_usuarios_notificaciones');
	}

}
