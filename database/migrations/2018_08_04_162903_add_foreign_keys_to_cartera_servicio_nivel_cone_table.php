<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCarteraServicioNivelConeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cartera_servicio_nivel_cone', function(Blueprint $table)
		{
			$table->foreign('cartera_servicios_id', 'cartera_servicio_nivel_cone_ibfk_1')->references('id')->on('cartera_servicios')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('niveles_cones_id', 'cartera_servicio_nivel_cone_ibfk_2')->references('id')->on('niveles_cones')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cartera_servicio_nivel_cone', function(Blueprint $table)
		{
			$table->dropForeign('cartera_servicio_nivel_cone_ibfk_1');
			$table->dropForeign('cartera_servicio_nivel_cone_ibfk_2');
		});
	}

}
