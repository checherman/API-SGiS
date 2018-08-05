<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToTriageColorTriageSintomaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('triage_color_triage_sintoma', function(Blueprint $table)
		{
			$table->foreign('triage_colores_id', 'triage_color_triage_sintoma_ibfk_1')->references('id')->on('triage_colores')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('triage_sintomas_id', 'triage_color_triage_sintoma_ibfk_2')->references('id')->on('triage_sintomas')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('triage_color_triage_sintoma', function(Blueprint $table)
		{
			$table->dropForeign('triage_color_triage_sintoma_ibfk_1');
			$table->dropForeign('triage_color_triage_sintoma_ibfk_2');
		});
	}

}
