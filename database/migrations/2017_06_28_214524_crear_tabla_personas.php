<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaPersonas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personas', function (Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->string('id');
            $table->string('servidor_id',4);
            $table->string('nombre');
            $table->string('paterno');
            $table->string('materno');
            $table->string('domicilio');
            $table->date('fecha_nacimiento')->nullable();
            $table->string('telefono',10);
            $table->integer('estados_embarazos_id')->unsigned();
            $table->integer('derechohabientes_id')->unsigned();

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');

            $table->foreign('estados_embarazos_id')->references('id')->on('estados_embarazos')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('derechohabientes_id')->references('id')->on('derechohabientes')
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
        Schema::drop('personas');
    }
}
