<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePersonasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('personas', function(Blueprint $table)
		{
			$table->string('id', 45)->primary();
			$table->string('nombre', 191);
			$table->string('paterno', 191);
			$table->string('materno', 191);
			$table->string('domicilio', 191);
			$table->date('fecha_nacimiento')->nullable();
			$table->string('telefono', 10);
			$table->integer('estados_embarazos_id')->unsigned()->nullable()->index('personas_estados_embarazos_id_foreign');
			$table->integer('derechohabientes_id')->unsigned()->nullable()->index('personas_derechohabientes_id_foreign');
			$table->integer('municipios_id')->nullable();
			$table->integer('localidades_id')->unsigned()->index('personas_localidades_id_foreign');
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
		Schema::drop('personas');
	}

}
