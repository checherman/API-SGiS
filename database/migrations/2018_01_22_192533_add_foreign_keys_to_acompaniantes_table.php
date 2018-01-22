<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAcompaniantesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('acompaniantes', function(Blueprint $table)
		{
			$table->foreign('parentescos_id', 'fk_acompaniante_parentesco')->references('id')->on('parentescos')->onUpdate('CASCADE')->onDelete('NO ACTION');
			$table->foreign('personas_id', 'fk_acompaniantes_personas_id')->references('id')->on('personas')->onUpdate('CASCADE')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('acompaniantes', function(Blueprint $table)
		{
			$table->dropForeign('fk_acompaniante_parentesco');
			$table->dropForeign('fk_acompaniantes_personas_id');
		});
	}

}
