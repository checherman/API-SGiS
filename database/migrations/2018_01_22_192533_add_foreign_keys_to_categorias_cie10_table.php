<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCategoriasCie10Table extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('categorias_cie10', function(Blueprint $table)
		{
			$table->foreign('grupos_cie10_id')->references('id')->on('grupos_cie10')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('categorias_cie10', function(Blueprint $table)
		{
			$table->dropForeign('categorias_cie10_grupos_cie10_id_foreign');
		});
	}

}
