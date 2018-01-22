<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCluesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('clues', function(Blueprint $table)
		{
			$table->string('clues', 12)->primary()->comment('CLave Unica de Establecimientos de Salud');
			$table->string('nombre', 120)->comment('Nombre de la unidad de salud');
			$table->string('domicilio', 200)->comment('Direccion de la unidad de salud, calle, numero, colonia, ciudad o municipio.');
			$table->integer('codigoPostal');
			$table->float('numeroLongitud', 10, 0)->nullable();
			$table->float('numeroLatitud', 10, 0)->nullable();
			$table->string('entidad', 50);
			$table->string('estado', 60);
			$table->string('institucion', 80);
			$table->integer('jurisdicciones_id')->unsigned()->comment('JurIsdiccion al que pertenece la CLUES');
			$table->string('localidad', 70);
			$table->integer('municipios_id')->unsigned()->comment('Municipio al que pertenece la CLUES');
			$table->string('tipologia', 70);
			$table->integer('nivel_cone_id')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('clues');
	}

}
