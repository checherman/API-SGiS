<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaLocalidades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('localidades', function (Blueprint $table) {

            $table->increments('id');
            $table->string('clave');
            $table->string('nombre');
            $table->double('numeroLatitud');
            $table->double('numeroLongitud');
            $table->integer('numeroAltitud');
            $table->string('claveCarta',6);
            $table->integer('entidades_id')->default(7);
            $table->integer('municipios_id')->unsigned();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('municipios_id')->references('id')->on('municipios')
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
        Schema::dropIfExists('localidades');
    }
}
