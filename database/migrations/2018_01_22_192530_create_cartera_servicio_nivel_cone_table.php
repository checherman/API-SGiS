<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCarteraServicioNivelConeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cartera_servicio_nivel_cone', function(Blueprint $table)
		{
			$table->integer('cartera_servicios_id')->unsigned()->index('cartera_servicio_nivel_cone_cartera_servicios_id_foreign');
			$table->integer('niveles_cones_id')->unsigned()->index('cartera_servicio_nivel_cone_niveles_cones_id_foreign');
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
		Schema::drop('cartera_servicio_nivel_cone');
	}

}
