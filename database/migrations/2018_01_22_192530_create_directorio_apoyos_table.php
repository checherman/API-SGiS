<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDirectorioApoyosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('directorio_apoyos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('institucion', 191);
			$table->string('direccion', 191);
			$table->string('responsable', 191);
			$table->string('telefono', 191);
			$table->string('correo', 191);
			$table->integer('municipios_id')->unsigned();
			$table->integer('localidades_id')->nullable();
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
		Schema::drop('directorio_apoyos');
	}

}
