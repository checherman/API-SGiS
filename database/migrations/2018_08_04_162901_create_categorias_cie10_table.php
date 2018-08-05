<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCategoriasCie10Table extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('categorias_cie10', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('grupos_cie10_id')->unsigned()->index('categorias_cie10_grupos_cie10_id_foreign');
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
		Schema::drop('categorias_cie10');
	}

}
