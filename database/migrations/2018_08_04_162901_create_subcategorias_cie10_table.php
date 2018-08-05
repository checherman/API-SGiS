<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubcategoriasCie10Table extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('subcategorias_cie10', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('categorias_cie10_id')->unsigned()->index('subcategorias_cie10_categorias_cie10_id_foreign');
			$table->string('codigo');
			$table->text('nombre');
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('subcategorias_cie10');
	}

}
