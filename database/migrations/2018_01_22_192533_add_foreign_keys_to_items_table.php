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
			$table->foreign('cartera_servicios_id')->references('id')->on('cartera_servicios')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('tipos_items_id')->references('id')->on('tipos_items')->onUpdate('CASCADE')->onDelete('CASCADE');
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
			$table->dropForeign('items_cartera_servicios_id_foreign');
			$table->dropForeign('items_tipos_items_id_foreign');
		});
	}

}
