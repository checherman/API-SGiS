<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVisitasPuerperalesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('visitas_puerperales', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('altas_incidencias_id')->unsigned()->index('fk_visitas_puerperales_altas_incidencias_idx');
			$table->date('fecha_visita')->nullable();
			$table->boolean('seAtendio')->nullable();
			$table->string('porque', 191)->nullable();
			$table->string('observaciones', 191)->nullable();
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
		Schema::drop('visitas_puerperales');
	}

}
