<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVersionesAppsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('versiones_apps', function(Blueprint $table)
		{
			$table->integer('id')->primary();
			$table->string('nombre', 150)->nullable();
			$table->string('ruta', 145)->nullable();
			$table->string('version_app', 45)->nullable();
			$table->string('version_db', 45)->nullable();
			$table->string('descripcion', 345)->nullable();
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
		Schema::drop('versiones_apps');
	}

}
