<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEstadosFuerzaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('estados_fuerza', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('clues', 191)->nullable();
			$table->integer('turnos_id')->unsigned()->nullable()->index('fk_estados_fuerza_turnos_id_idx');
			$table->integer('sis_usuarios_id')->nullable();
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
		Schema::drop('estados_fuerza');
	}

}
