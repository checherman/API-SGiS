<?php

use App\Models\Catalogos\EstadosIncidencias;
use Illuminate\Database\Seeder;

class CatalogoEstadosIncidenciasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EstadosIncidencias::create( [
            'id'=>1,
            'nombre'=>'Nueva',
            'descripcion'=>NULL,
            'created_at'=>'2017-08-07 17:40:24',
            'updated_at'=>'2017-08-07 17:40:24',
            'deleted_at'=>NULL
        ] );



        EstadosIncidencias::create( [
            'id'=>2,
            'nombre'=>'En proceso',
            'descripcion'=>NULL,
            'created_at'=>'2017-08-07 17:40:33',
            'updated_at'=>'2017-08-07 17:40:33',
            'deleted_at'=>NULL
        ] );



        EstadosIncidencias::create( [
            'id'=>3,
            'nombre'=>'Finalizada',
            'descripcion'=>NULL,
            'created_at'=>'2017-08-07 17:40:41',
            'updated_at'=>'2017-08-07 17:40:41',
            'deleted_at'=>NULL
        ] );



        EstadosIncidencias::create( [
            'id'=>4,
            'nombre'=>'Referencia',
            'descripcion'=>'Se envio la paciente a otra unidad',
            'created_at'=>'2017-08-07 17:40:41',
            'updated_at'=>'2018-02-18 20:12:54',
            'deleted_at'=>NULL
        ] );


        EstadosIncidencias::create( [
            'id'=>5,
            'nombre'=>'Puerperio',
            'descripcion'=>'',
            'created_at'=>'2017-11-21 20:34:21',
            'updated_at'=>'2017-11-21 20:44:05',
            'deleted_at'=>NULL
        ] );
    }
}
