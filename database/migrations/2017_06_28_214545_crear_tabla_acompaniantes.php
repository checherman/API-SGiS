<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaAcompaniantes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acompaniantes', function (Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->string('id');
            $table->string('servidor_id',4);
            $table->integer('incremento');
            $table->string('personas_id');
            $table->integer('parentescos_id')->unsigned();
            $table->boolean('esResponsable');

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');

            $table->foreign('personas_id')->references('id')->on('personas')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('parentescos_id')->references('id')->on('parentescos')
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
        Schema::drop('acompaniantes');
    }
}
