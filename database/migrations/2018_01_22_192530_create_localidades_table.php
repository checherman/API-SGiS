<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLocalidadesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('localidades', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('clave', 191);
			$table->string('nombre', 191);
			$table->float('numeroLatitud', 10, 0);
			$table->float('numeroLongitud', 10, 0);
			$table->integer('numeroAltitud');
			$table->string('claveCarta', 6);
			$table->integer('entidades_id')->default(7);
			$table->integer('municipios_id')->unsigned()->index('localidades_municipios_id_foreign');
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
		Schema::drop('localidades');
	}

}
