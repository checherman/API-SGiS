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
			$table->foreign('derechohabientes_id', 'fk_personas_derechohabientes_id')->references('id')->on('derechohabientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('estados_embarazos_id', 'fk_personas_estados_embarazos_id')->references('id')->on('estados_embarazos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
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
			$table->dropForeign('fk_personas_derechohabientes_id');
			$table->dropForeign('fk_personas_estados_embarazos_id');
		});
	}

}
