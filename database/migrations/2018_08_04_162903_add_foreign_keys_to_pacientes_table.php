<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPacientesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('pacientes', function(Blueprint $table)
		{
			$table->foreign('personas_id', 'pacientes_ibfk_1')->references('id')->on('personas')->onUpdate('CASCADE')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('pacientes', function(Blueprint $table)
		{
			$table->dropForeign('pacientes_ibfk_1');
		});
	}

}
