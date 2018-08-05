<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToTriageSintomasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('triage_sintomas', function(Blueprint $table)
		{
			$table->foreign('triage_id', 'triage_sintomas_ibfk_1')->references('id')->on('triage')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('triage_sintomas', function(Blueprint $table)
		{
			$table->dropForeign('triage_sintomas_ibfk_1');
		});
	}

}
