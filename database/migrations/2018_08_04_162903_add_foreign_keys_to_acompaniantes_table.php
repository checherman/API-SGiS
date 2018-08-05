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
			$table->foreign('parentescos_id', 'acompaniantes_ibfk_1')->references('id')->on('parentescos')->onUpdate('CASCADE')->onDelete('NO ACTION');
			$table->foreign('personas_id', 'acompaniantes_ibfk_2')->references('id')->on('personas')->onUpdate('CASCADE')->onDelete('NO ACTION');
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
			$table->dropForeign('acompaniantes_ibfk_1');
			$table->dropForeign('acompaniantes_ibfk_2');
		});
	}

}
