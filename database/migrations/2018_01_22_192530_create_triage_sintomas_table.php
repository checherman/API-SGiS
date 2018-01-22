<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTriageSintomasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('triage_sintomas', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('nombre', 191);
			$table->integer('triage_id')->unsigned()->index('triage_sintomas_triage_id_foreign');
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
		Schema::drop('triage_sintomas');
	}

}
