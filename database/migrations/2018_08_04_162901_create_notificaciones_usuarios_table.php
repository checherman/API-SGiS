<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNotificacionesUsuariosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('notificaciones_usuarios', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('usuarios_id')->nullable();
			$table->integer('notificaciones_id')->index('fk_Laravelcreated_at_notificaciones1_idx');
			$table->dateTime('enviado')->nullable();
			$table->dateTime('leido')->nullable();
			$table->string('telefono', 10)->nullable();
			$table->string('sms');
			$table->timestamps();
			$table->softDeletes();
			$table->integer('creado_por');
			$table->integer('modificado_por');
			$table->integer('borrado_por');
			$table->integer('status')->nullable()->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('notificaciones_usuarios');
	}

}
