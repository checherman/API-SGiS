<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaIncidencias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incidencias', function (Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->string('id');
            $table->string('servidor_id',4);
            $table->string('motivo_ingreso');
            $table->string('impresion_diagnostica');
            $table->integer('estados_incidencias_id')->unsigned()->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');


            $table->foreign('estados_incidencias_id')->references('id')->on('estados_incidencias')
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
        Schema::drop('incidencias');
    }
}
