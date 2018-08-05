<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaisesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('paises', function(Blueprint $table)
		{
			$table->integer('id')->unsigned();
			$table->string('nombre', 150)->nullable();
			$table->string('clave_ISOA2', 2)->nullable();
			$table->string('clave_A3', 3)->nullable();
			$table->string('clave_N3', 5)->nullable();
			$table->string('prefijo_telefono', 5)->nullable();
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
		Schema::drop('paises');
	}

}
