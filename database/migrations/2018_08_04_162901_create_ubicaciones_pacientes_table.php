<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUbicacionesPacientesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ubicaciones_pacientes', function(Blueprint $table)
		{
			$table->integer('id')->unsigned()->primary();
			$table->string('nombre');
			$table->string('descripcion')->nullable();
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
		Schema::drop('ubicaciones_pacientes');
	}

}
