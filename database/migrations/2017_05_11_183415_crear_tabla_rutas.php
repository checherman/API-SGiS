<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaRutas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rutas', function (Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('nombre');
            $table->string('clues_origen');
            $table->string('clues_destino');
            $table->string('tiempo_traslado',45);
            $table->string('distancia_traslado',45);
            $table->string('observaciones');

            $table->timestamps();
            $table->softDeletes();

//            $table->foreign('clues_origen')->references('clues')->on('clues')
//                ->onDelete('cascade')
//                ->onUpdate('cascade');
//
//            $table->foreign('clues_destino')->references('clues')->on('clues')
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
        Schema::drop('rutas');
    }
}
