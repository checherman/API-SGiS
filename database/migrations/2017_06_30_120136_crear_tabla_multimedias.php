<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaMultimedias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('multimedias', function (Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->string('id');
            $table->string('servidor_id',4);
            $table->integer('incremento');
            $table->string('referencias_id')->nullable();
            $table->string('altas_incidencias_id')->nullable();
            $table->string('tipo');
            $table->string('url');

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');

            $table->foreign('referencias_id')->references('id')->on('referencias')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('altas_incidencias_id')->references('id')->on('altas_incidencias')
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
        Schema::drop('multimedias');
    }
}
