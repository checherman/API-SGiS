<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaDirectorioApoyos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directorio_apoyos', function (Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->string('id');
            $table->string('servidor_id',4);
            $table->string('institucion');
            $table->integer('incremento');
            $table->string('direccion');
            $table->string('responsable');
            $table->string('telefono');
            $table->string('correo');
            $table->integer('municipios_id')->unsigned();

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');

//            $table->foreign('municipios_id')->references('id')->on('municipios')
//                ->onDelete('cascade')
//                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('directorio_apoyos');
    }
}
