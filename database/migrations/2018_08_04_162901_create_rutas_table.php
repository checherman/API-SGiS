<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRutasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('rutas', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('nombre', 191);
			$table->string('clues_origen', 191);
			$table->float('numeroLatitud_origen', 10, 0);
			$table->float('numeroLongitud_origen', 10, 0);
			$table->string('clues_destino', 191);
			$table->float('numeroLatitud_destino', 10, 0);
			$table->float('numeroLongitud_destino', 10, 0);
			$table->string('tiempo_traslado', 45);
			$table->string('distancia_traslado', 45);
			$table->string('observaciones', 191);
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
		Schema::drop('rutas');
	}

}
