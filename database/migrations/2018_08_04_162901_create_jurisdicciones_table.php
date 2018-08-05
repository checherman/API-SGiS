<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJurisdiccionesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('jurisdicciones', function(Blueprint $table)
		{
			$table->integer('id')->unsigned()->primary();
			$table->string('clave', 2);
			$table->string('nombre', 50);
			$table->integer('entidades_id')->unsigned();
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
		Schema::drop('jurisdicciones');
	}

}
