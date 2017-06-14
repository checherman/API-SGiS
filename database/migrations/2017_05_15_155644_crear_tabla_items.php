<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('nombre', 255);
            $table->integer('cartera_servicios_id')->unsigned();
            $table->integer('tipos_items_id')->unsigned();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('cartera_servicios_id')->references('id')->on('cartera_servicios')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('tipos_items_id')->references('id')->on('tipos_items')
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
        Schema::dropIfExists('items');
    }
}
