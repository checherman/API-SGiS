<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaPivoteApoyoDirectorioApoyo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apoyo_directorio_apoyo', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->integer('apoyos_id')->unsigned();
            $table->string('directorio_apoyos_id');

            $table->foreign('apoyos_id')
                ->references('id')->on('apoyos')
                ->onDelete('cascade');

            $table->foreign('directorio_apoyos_id')
                ->references('id')->on('directorio_apoyos')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('apoyo_directorio_apoyo');
    }
}
