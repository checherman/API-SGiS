<?php

use App\Models\Catalogos\Derechohabientes;
use Illuminate\Database\Seeder;

class CatalogoDerechohabientesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Derechohabientes::create( [
            'id'=>1,
            'nombre'=>'Seguro popular',
            'descripcion'=>NULL,
            'created_at'=>'2017-08-07 17:37:27',
            'updated_at'=>'2017-08-07 17:37:27',
            'deleted_at'=>NULL
        ] );



        Derechohabientes::create( [
            'id'=>2,
            'nombre'=>'Prospera',
            'descripcion'=>NULL,
            'created_at'=>'2017-08-07 17:37:38',
            'updated_at'=>'2017-08-07 17:37:38',
            'deleted_at'=>NULL
        ] );



        Derechohabientes::create( [
            'id'=>3,
            'nombre'=>'IMSS',
            'descripcion'=>'Instituto Mexicano del Seguro Social',
            'created_at'=>'2017-08-07 17:37:54',
            'updated_at'=>'2017-12-20 09:20:58',
            'deleted_at'=>NULL
        ] );



        Derechohabientes::create( [
            'id'=>4,
            'nombre'=>'NUEVA INSTITUCIÓN (MODIFICADA)',
            'descripcion'=>'ESTA ES UNA INSTITUCIÓN DE PRUEBA',
            'created_at'=>'2017-11-20 05:25:06',
            'updated_at'=>'2017-11-20 05:35:07',
            'deleted_at'=>'2017-11-20 05:35:07'
        ] );



        Derechohabientes::create( [
            'id'=>5,
            'nombre'=>'ISSSTE',
            'descripcion'=>'Instituto de Seguridad y Servicios Sociales de los Trabajadores del Estado',
            'created_at'=>'2017-12-01 21:58:10',
            'updated_at'=>'2017-12-01 21:59:15',
            'deleted_at'=>NULL
        ] );



        Derechohabientes::create( [
            'id'=>6,
            'nombre'=>'ISSTECH',
            'descripcion'=>'Instituto de Seguridad Social de los Trabajadores del Estado de Chiapas',
            'created_at'=>'2017-12-01 21:58:50',
            'updated_at'=>'2017-12-01 21:58:50',
            'deleted_at'=>NULL
        ] );



        Derechohabientes::create( [
            'id'=>7,
            'nombre'=>'Población Abierta',
            'descripcion'=>'Población abierta',
            'created_at'=>'2017-12-01 21:59:44',
            'updated_at'=>'2017-12-01 21:59:44',
            'deleted_at'=>NULL
        ] );
    }
}
