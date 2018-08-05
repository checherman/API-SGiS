<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSisGruposTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sis_grupos', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('nombre')->unique('name_UNIQUE');
			$table->text('permisos', 65535)->nullable();
			$table->timestamps();
			$table->softDeletes();
			$table->integer('creado_por');
			$table->integer('modificado_por');
			$table->integer('borrado_por');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sis_grupos');
	}

}
