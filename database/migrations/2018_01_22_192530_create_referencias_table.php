<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReferenciasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('referencias', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('incidencias_id', 50)->index('referencias_incidencias_id_foreign');
			$table->string('medico_refiere_id', 191)->nullable();
			$table->string('diagnostico', 191)->nullable();
			$table->string('resumen_clinico', 191)->nullable();
			$table->string('clues_origen', 191)->nullable();
			$table->string('clues_destino', 191)->nullable();
			$table->boolean('esIngreso')->nullable();
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
		Schema::drop('referencias');
	}

}
