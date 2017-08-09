<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaSubcategoriasCie10 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subcategorias_cie10', function (Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('categorias_cie10_id')->unsigned();
            $table->string('codigo', 255);
            $table->string('nombre');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('categorias_cie10_id')->references('id')->on('categorias_cie10')
                ->onDelete('cascade')
                ->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('subcategorias_cie10');
    }
}
