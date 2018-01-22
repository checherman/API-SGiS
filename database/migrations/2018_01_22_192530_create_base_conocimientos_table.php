<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBaseConocimientosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('base_conocimientos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('proceso', 191);
			$table->integer('triage_colores_id')->unsigned()->index('base_conocimientos_triage_colores_id_foreign');
			$table->integer('subcategorias_cie10_id')->unsigned()->index('base_conocimientos_subcategorias_cie10_id_foreign');
			$table->integer('estados_pacientes_id')->unsigned()->index('base_conocimientos_estados_pacientes_id_foreign');
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
		Schema::drop('base_conocimientos');
	}

}
