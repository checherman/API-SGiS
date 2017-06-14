<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablePivoteCarteraServicioNivelCone extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cartera_servicio_nivel_cone', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->integer('cartera_servicios_id')->unsigned();
            $table->integer('niveles_cones_id')->unsigned();

            $table->foreign('cartera_servicios_id')
                ->references('id')->on('cartera_servicios')
                ->onDelete('cascade');

            $table->foreign('niveles_cones_id')
                ->references('id')->on('niveles_cones')
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
        Schema::drop('cartera_servicio_nivel_cone');
    }
}
