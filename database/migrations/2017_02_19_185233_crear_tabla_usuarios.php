<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaUsuarios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->string('id');
            $table->string('servidor_id',4);
            $table->string('password',60)->nullable();
            $table->string('nombre');
            $table->string('paterno');
            $table->string('materno');
            $table->string('celular');
            $table->integer('cargos_id')->nullable()->unsigned();
            $table->string('avatar')->nullable();
            $table->string('clues');
            $table->boolean('su')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');
            $table->foreign('servidor_id')->references('id')->on('servidores')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('cargos_id')->references('id')->on('cargos')
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
        Schema::drop('usuarios');
    }
}
