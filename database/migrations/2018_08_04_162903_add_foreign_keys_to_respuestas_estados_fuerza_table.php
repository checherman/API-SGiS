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
			$table->foreign('cartera_servicios_id', 'respuestas_estados_fuerza_ibfk_1')->references('id')->on('cartera_servicios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('estados_fuerza_id', 'respuestas_estados_fuerza_ibfk_2')->references('id')->on('estados_fuerza')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('items_id', 'respuestas_estados_fuerza_ibfk_3')->references('id')->on('items')->onUpdate('NO ACTION')->onDelete('NO ACTION');
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
			$table->dropForeign('respuestas_estados_fuerza_ibfk_1');
			$table->dropForeign('respuestas_estados_fuerza_ibfk_2');
			$table->dropForeign('respuestas_estados_fuerza_ibfk_3');
		});
	}

}
