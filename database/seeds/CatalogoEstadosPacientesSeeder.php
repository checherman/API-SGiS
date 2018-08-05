<?php

use App\Models\Catalogos\EstadosPacientes;
use Illuminate\Database\Seeder;

class CatalogoEstadosPacientesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EstadosPacientes::create( [
            'id'=>1,
            'nombre'=>'Estable',
            'descripcion'=>'.',
            'created_at'=>'2017-08-07 17:38:40',
            'updated_at'=>'2017-08-07 17:38:40',
            'deleted_at'=>NULL
        ] );



        EstadosPacientes::create( [
            'id'=>2,
            'nombre'=>'Delicado',
            'descripcion'=>'.',
            'created_at'=>'2017-08-07 17:38:57',
            'updated_at'=>'2017-08-07 17:38:57',
            'deleted_at'=>NULL
        ] );



        EstadosPacientes::create( [
            'id'=>3,
            'nombre'=>'Grave',
            'descripcion'=>'..',
            'created_at'=>'2017-08-07 17:39:05',
            'updated_at'=>'2017-10-18 21:25:52',
            'deleted_at'=>NULL
        ] );


    }
}
