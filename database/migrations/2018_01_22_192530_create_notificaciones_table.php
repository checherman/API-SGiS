<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNotificacionesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('notificaciones', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('tipo', 150)->nullable();
			$table->text('mensaje', 65535)->nullable();
			$table->timestamps();
			$table->softDeletes();
			$table->integer('creado_por');
			$table->integer('modificado_por');
			$table->integer('borrado_por');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('notificaciones');
	}

}
