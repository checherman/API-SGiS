<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToBaseConocimientosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('base_conocimientos', function(Blueprint $table)
		{
			$table->foreign('estados_pacientes_id', 'base_conocimientos_ibfk_1')->references('id')->on('estados_pacientes')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('subcategorias_cie10_id', 'base_conocimientos_ibfk_2')->references('id')->on('subcategorias_cie10')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('triage_colores_id', 'base_conocimientos_ibfk_3')->references('id')->on('triage_colores')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('base_conocimientos', function(Blueprint $table)
		{
			$table->dropForeign('base_conocimientos_ibfk_1');
			$table->dropForeign('base_conocimientos_ibfk_2');
			$table->dropForeign('base_conocimientos_ibfk_3');
		});
	}

}
