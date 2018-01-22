<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAcompaniantesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('acompaniantes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('personas_id', 45)->index('acompaniantes_personas_id_foreign');
			$table->integer('parentescos_id')->unsigned()->index('acompaniantes_parentescos_id_foreign');
			$table->boolean('esResponsable');
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
		Schema::drop('acompaniantes');
	}

}
