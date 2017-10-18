<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaSisGrupos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_grupos', function (Blueprint $table) {

            $table->increments('id')->unsigned();

            $table->string('nombre');
            $table->text('permisos');

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
        Schema::dropIfExists('sis_grupos');
    }
}
