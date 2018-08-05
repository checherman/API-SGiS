<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEstadosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('estados', function(Blueprint $table)
		{
			$table->integer('id')->unsigned();
			$table->integer('paises_id')->unsigned();
			$table->string('clave', 2);
			$table->string('nombre', 50);
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
		Schema::drop('estados');
	}

}
