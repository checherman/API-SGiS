<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSisUsuariosContactosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sis_usuarios_contactos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('sis_usuarios_id')->unsigned();
			$table->integer('tipos_medios_id');
			$table->string('valor', 150)->nullable();
			$table->timestamps();
			$table->softDeletes();
			$table->integer('creado_por')->nullable();
			$table->integer('modificado_por')->nullable();
			$table->integer('borrado_por')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sis_usuarios_contactos');
	}

}
