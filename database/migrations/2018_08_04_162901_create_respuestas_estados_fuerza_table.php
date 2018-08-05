<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRespuestasEstadosFuerzaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('respuestas_estados_fuerza', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('estados_fuerza_id')->unsigned()->nullable()->index('fk_respuestas_estados_fuerza_estados_fuerza_id_idx');
			$table->integer('cartera_servicios_id')->unsigned()->nullable()->index('fk_respuestas_estados_fuerza_cartera_servicios_id_idx');
			$table->integer('items_id')->unsigned()->index('respuestas_estados_fuerza_items_id_foreign');
			$table->string('respuesta', 191);
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
		Schema::drop('respuestas_estados_fuerza');
	}

}
