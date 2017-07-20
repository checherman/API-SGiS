<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaBaseConocimientos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('base_conocimientos', function (Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->string('id');
            $table->string('servidor_id',4);
            $table->integer('incremento');
            $table->string('procesos');
            $table->integer('triage_colores_id')->unsigned();
            $table->integer('subcategorias_cie10_id')->unsigned();
            $table->integer('valoraciones_pacientes_id')->unsigned();
            $table->integer('estados_pacientes_id')->unsigned();

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');

            $table->foreign('triage_colores_id')->references('id')->on('triage_colores')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('subcategorias_cie10_id')->references('id')->on('subcategorias_cie10')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('valoraciones_pacientes_id')->references('id')->on('valoraciones_pacientes')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('estados_pacientes_id')->references('id')->on('estados_pacientes')
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
        Schema::drop('base_conocimientos');
    }
}
