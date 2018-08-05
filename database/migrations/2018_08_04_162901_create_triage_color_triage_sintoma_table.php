<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTriageColorTriageSintomaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('triage_color_triage_sintoma', function(Blueprint $table)
		{
			$table->string('nombre', 191);
			$table->integer('triage_sintomas_id')->unsigned()->index('triage_color_triage_sintoma_triage_sintomas_id_foreign');
			$table->integer('triage_colores_id')->unsigned()->index('triage_color_triage_sintoma_triage_colores_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('triage_color_triage_sintoma');
	}

}
