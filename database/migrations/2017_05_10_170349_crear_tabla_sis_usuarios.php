<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaSisUsuarios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_usuarios', function (Blueprint $table) {

            $table->increments('id')->unsigned();
            $table->integer('sis_modulos_id')->unsigned();

            $table->string('nombre');
            $table->string('username',45);
            $table->string('email');
            $table->string('password');
            $table->string('direccion',150);
            $table->string('numero_exterior',45);
            $table->string('numero_interior',45);
            $table->string('colonia',45);
            $table->string('codigo_postal',45);
            $table->string('comentario');
            $table->string('foto',250);
            $table->boolean('spam');
            $table->integer('entidades_id')->default(7);
            $table->integer('paises_id');
            $table->integer('jurisdicciones_id');
            $table->boolean('es_super');
            $table->boolean('activo');
            $table->string('avatar');
            $table->string('reset_password_code');
            $table->string('persist_code');
            $table->timestamp('last_login');
            $table->timestamp('activated_at');
            $table->string('activation_code');
            $table->boolean('activated');
            $table->text('permissions');
            $table->string('remember_token');


            $table->integer('creado_por');
            $table->integer('modificado_por');
            $table->integer('borrado_por');

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
        Schema::dropIfExists('sis_usuarios');
    }
}
