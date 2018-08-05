<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSisUsuariosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sis_usuarios', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('nombre')->nullable();
			$table->string('username', 45)->nullable();
			$table->string('email')->unique('users_email_unique');
			$table->string('password')->nullable();
			$table->string('direccion', 150)->nullable();
			$table->string('numero_exterior', 45)->nullable();
			$table->string('numero_interior', 45)->nullable();
			$table->string('colonia', 45)->nullable();
			$table->string('codigo_postal', 45)->nullable();
			$table->string('comentario')->nullable();
			$table->string('foto', 250)->nullable();
			$table->boolean('spam')->nullable();
			$table->integer('localidades_id')->unsigned();
			$table->integer('estados_id')->unsigned()->nullable();
			$table->integer('municipios_id')->unsigned()->nullable();
			$table->boolean('es_super')->nullable();
			$table->boolean('activo')->nullable();
			$table->string('avatar')->nullable();
			$table->string('reset_password_code')->nullable();
			$table->string('persist_code')->nullable();
			$table->dateTime('last_login')->nullable();
			$table->dateTime('activated_at')->nullable();
			$table->string('activation_code')->nullable();
			$table->boolean('activated')->default(0);
			$table->text('permisos', 65535)->nullable();
			$table->string('remember_token');
			$table->timestamps();
			$table->softDeletes();
			$table->integer('creado_por')->nullable();
			$table->integer('modificado_por')->nullable();
			$table->integer('borrado_por')->nullable();
			$table->integer('cargos_id')->unsigned()->nullable()->index('fk_sis_usuarios_cargos1_idx');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sis_usuarios');
	}

}
