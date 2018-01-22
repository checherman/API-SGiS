<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDiasFestivosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('dias_festivos', function(Blueprint $table)
		{
			$table->foreign('turno_id')->references('id')->on('turnos')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('dias_festivos', function(Blueprint $table)
		{
			$table->dropForeign('dias_festivos_turno_id_foreign');
		});
	}

}
