<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTriageColoresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('triage_colores', function(Blueprint $table)
		{
			$table->integer('id')->unsigned()->primary();
			$table->string('nombre', 191);
			$table->string('descripcion', 191)->nullable();
			$table->time('tiempo_minimo');
			$table->time('tiempo_maximo');
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
		Schema::drop('triage_colores');
	}

}
