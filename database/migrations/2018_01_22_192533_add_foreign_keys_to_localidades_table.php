<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToLocalidadesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('localidades', function(Blueprint $table)
		{
			$table->foreign('municipios_id', 'localidades_ibfk_1')->references('id')->on('municipios')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('localidades', function(Blueprint $table)
		{
			$table->dropForeign('localidades_ibfk_1');
		});
	}

}
