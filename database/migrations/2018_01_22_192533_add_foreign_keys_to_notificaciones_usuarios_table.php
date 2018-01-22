<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToNotificacionesUsuariosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('notificaciones_usuarios', function(Blueprint $table)
		{
			$table->foreign('notificaciones_id', 'fk_Laravel created_at_notificaciones1')->references('id')->on('notificaciones')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('notificaciones_usuarios', function(Blueprint $table)
		{
			$table->dropForeign('fk_Laravel created_at_notificaciones1');
		});
	}

}
