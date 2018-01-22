<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToRespuestasEstadosFuerzaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('respuestas_estados_fuerza', function(Blueprint $table)
		{
			$table->foreign('cartera_servicios_id', 'fk_respuestas_estados_fuerza_cartera_servicios_id')->references('id')->on('cartera_servicios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('estados_fuerza_id', 'fk_respuestas_estados_fuerza_estados_fuerza_id')->references('id')->on('estados_fuerza')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('items_id', 'fk_respuestas_estados_fuerza_items_id')->references('id')->on('items')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('respuestas_estados_fuerza', function(Blueprint $table)
		{
			$table->dropForeign('fk_respuestas_estados_fuerza_cartera_servicios_id');
			$table->dropForeign('fk_respuestas_estados_fuerza_estados_fuerza_id');
			$table->dropForeign('fk_respuestas_estados_fuerza_items_id');
		});
	}

}
