<?php

use App\Models\Catalogos\UbicacionesPacientes;
use Illuminate\Database\Seeder;

class CatalogoUbicacionesPacienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UbicacionesPacientes::create( [
            'id'=>1,
            'nombre'=>'En espera',
            'descripcion'=>NULL,
            'created_at'=>'2017-08-07 17:39:20',
            'updated_at'=>'2017-08-07 17:39:20',
            'deleted_at'=>NULL
        ] );



        UbicacionesPacientes::create( [
            'id'=>2,
            'nombre'=>'En atención',
            'descripcion'=>NULL,
            'created_at'=>'2017-08-07 17:39:30',
            'updated_at'=>'2017-08-07 17:39:30',
            'deleted_at'=>NULL
        ] );



        UbicacionesPacientes::create( [
            'id'=>3,
            'nombre'=>'En traslado - Referencia',
            'descripcion'=>NULL,
            'created_at'=>'2017-08-07 17:39:41',
            'updated_at'=>'2017-10-20 19:03:19',
            'deleted_at'=>NULL
        ] );



        UbicacionesPacientes::create( [
            'id'=>4,
            'nombre'=>'En traslado - Contrareferencia',
            'descripcion'=>NULL,
            'created_at'=>'2017-08-07 17:39:20',
            'updated_at'=>'2017-10-17 18:27:31',
            'deleted_at'=>NULL
        ] );



        UbicacionesPacientes::create( [
            'id'=>5,
            'nombre'=>'En hospitalización',
            'descripcion'=>NULL,
            'created_at'=>'2017-08-07 17:39:30',
            'updated_at'=>'2017-08-07 17:39:30',
            'deleted_at'=>NULL
        ] );



        UbicacionesPacientes::create( [
            'id'=>6,
            'nombre'=>'En urgencias',
            'descripcion'=>NULL,
            'created_at'=>'2017-08-07 17:39:41',
            'updated_at'=>'2017-08-07 17:39:41',
            'deleted_at'=>NULL
        ] );



        UbicacionesPacientes::create( [
            'id'=>7,
            'nombre'=>'En recuperación',
            'descripcion'=>NULL,
            'created_at'=>'2017-08-07 17:39:20',
            'updated_at'=>'2017-08-07 17:39:20',
            'deleted_at'=>NULL
        ] );



        UbicacionesPacientes::create( [
            'id'=>15,
            'nombre'=>'Ingreso al Consultorio',
            'descripcion'=>'La paciente es atendida después de esperar',
            'created_at'=>'2017-11-21 21:47:07',
            'updated_at'=>'2017-11-21 21:47:07',
            'deleted_at'=>NULL
        ] );
    }
}
