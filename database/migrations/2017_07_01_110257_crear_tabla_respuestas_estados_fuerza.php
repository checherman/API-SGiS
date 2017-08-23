<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaRespuestasEstadosFuerza extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('respuestas_estados_fuerza', function (Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->string('id');
            $table->string('servidor_id',4);
            $table->integer('incremento');
            $table->string('clues');
            $table->integer('respuesta_numero')->nullable();
            $table->string('respuesta_texto')->nullable();
            $table->boolean('respuesta_boolean')->nullable();
            $table->integer('items_id')->unsigned();
            $table->integer('turnos_id')->unsigned();

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');

            $table->foreign('items_id')->references('id')->on('items')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('turnos_id')->references('id')->on('turnos')
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
        Schema::drop('respuestas_estados_fuerza');
    }
}
