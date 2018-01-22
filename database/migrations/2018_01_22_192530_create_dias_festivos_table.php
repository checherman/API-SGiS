<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDiasFestivosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('dias_festivos', function(Blueprint $table)
		{
			$table->integer('turno_id')->unsigned()->nullable()->index('dias_festivos_turno_id_foreign');
			$table->string('nombre', 191);
			$table->date('fecha');
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
		Schema::drop('dias_festivos');
	}

}
