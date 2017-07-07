<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaJurisdiciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jurisdicciones', function (Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->integer('id')->unique();
            $table->string('clave', 2);
            $table->string('nombre', 255);
            $table->integer('entidades_id')->unsigned()->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jurisdicciones');
    }
}
