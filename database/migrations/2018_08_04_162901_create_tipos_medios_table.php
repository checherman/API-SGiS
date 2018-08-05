<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTiposMediosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tipos_medios', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('nombre')->nullable();
			$table->timestamps();
			$table->softDeletes();
			$table->string('icono')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tipos_medios');
	}

}
