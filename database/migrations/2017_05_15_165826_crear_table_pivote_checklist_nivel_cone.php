<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablePivoteChecklistNivelCone extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklist_nivel_cone', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->integer('checklists_id')->unsigned();
            $table->integer('niveles_cones_id')->unsigned();

            $table->foreign('checklists_id')
                ->references('id')->on('checklists')
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
        Schema::drop('checklist_nivel_cone');
    }
}
