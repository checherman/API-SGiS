<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPersonasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('personas', function(Blueprint $table)
		{
			$table->foreign('derechohabientes_id', 'personas_ibfk_1')->references('id')->on('derechohabientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('estados_embarazos_id', 'personas_ibfk_2')->references('id')->on('estados_embarazos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('personas', function(Blueprint $table)
		{
			$table->dropForeign('personas_ibfk_1');
			$table->dropForeign('personas_ibfk_2');
		});
	}

}
