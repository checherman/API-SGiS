<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('items', function(Blueprint $table)
		{
			$table->foreign('cartera_servicios_id', 'items_ibfk_1')->references('id')->on('cartera_servicios')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('tipos_items_id', 'items_ibfk_2')->references('id')->on('tipos_items')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('items', function(Blueprint $table)
		{
			$table->dropForeign('items_ibfk_1');
			$table->dropForeign('items_ibfk_2');
		});
	}

}
