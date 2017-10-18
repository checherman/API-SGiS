<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaSisUsuariosContactos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_usuarios_contactos', function (Blueprint $table) {

            $table->increments('id')->unsigned();
            $table->integer('sis_usuarios_id')->unsigned();
            $table->integer('tipos_medios_id')->unsigned();

            $table->string('valor',150);

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
        Schema::dropIfExists('sis_usuarios_contactos');
    }
}
