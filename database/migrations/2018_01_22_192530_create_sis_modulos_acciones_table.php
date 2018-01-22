<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSisModulosAccionesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sis_modulos_acciones', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('sis_modulos_id')->index('fk_sis_modulos_acciones_sis_modulos1_idx');
			$table->string('nombre', 150)->nullable();
			$table->boolean('es_super')->nullable();
			$table->string('recurso', 150)->nullable();
			$table->string('metodo', 150)->nullable();
			$table->timestamps();
			$table->softDeletes();
			$table->integer('creado_por');
			$table->integer('modificado_por');
			$table->integer('borrado_por');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sis_modulos_acciones');
	}

}
