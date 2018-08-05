<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSubcategoriasCie10Table extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('subcategorias_cie10', function(Blueprint $table)
		{
			$table->foreign('categorias_cie10_id', 'subcategorias_cie10_ibfk_1')->references('id')->on('categorias_cie10')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('subcategorias_cie10', function(Blueprint $table)
		{
			$table->dropForeign('subcategorias_cie10_ibfk_1');
		});
	}

}
