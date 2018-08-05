<?php

use App\Models\Catalogos\CarteraServicios;
use Illuminate\Database\Seeder;

class CatalogoCarteraServiciosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CarteraServicios::create( [
            'id'=>1,
            'nombre'=>'Personal',
            'created_at'=>'2017-11-13 06:01:06',
            'updated_at'=>'2017-11-13 06:01:06',
            'deleted_at'=>NULL
        ] );



        CarteraServicios::create( [
            'id'=>2,
            'nombre'=>'Camas',
            'created_at'=>'2017-11-13 06:02:30',
            'updated_at'=>'2017-11-13 06:02:30',
            'deleted_at'=>NULL
        ] );



        CarteraServicios::create( [
            'id'=>3,
            'nombre'=>'Equipo de traslado (Ambulancia)',
            'created_at'=>'2017-11-13 06:03:52',
            'updated_at'=>'2017-11-13 06:03:52',
            'deleted_at'=>NULL
        ] );



        CarteraServicios::create( [
            'id'=>4,
            'nombre'=>'Laboratorio',
            'created_at'=>'2017-11-13 06:06:06',
            'updated_at'=>'2017-11-13 06:06:06',
            'deleted_at'=>NULL
        ] );



        CarteraServicios::create( [
            'id'=>5,
            'nombre'=>'Hemoderivados (Sangre o Plasma)',
            'created_at'=>'2017-11-13 06:07:34',
            'updated_at'=>'2017-11-13 06:07:34',
            'deleted_at'=>NULL
        ] );



        CarteraServicios::create( [
            'id'=>6,
            'nombre'=>'Medicamentos',
            'created_at'=>'2017-11-13 06:08:08',
            'updated_at'=>'2017-11-13 06:08:08',
            'deleted_at'=>NULL
        ] );



        CarteraServicios::create( [
            'id'=>7,
            'nombre'=>'Módulo Máter',
            'created_at'=>'2017-11-13 06:08:38',
            'updated_at'=>'2017-11-13 06:13:30',
            'deleted_at'=>NULL
        ] );



        CarteraServicios::create( [
            'id'=>8,
            'nombre'=>'Atención de Preeclampsia-eclampsia',
            'created_at'=>'2017-11-13 06:09:05',
            'updated_at'=>'2017-11-13 06:09:05',
            'deleted_at'=>NULL
        ] );



        CarteraServicios::create( [
            'id'=>9,
            'nombre'=>'Área de Labor',
            'created_at'=>'2017-11-13 06:09:31',
            'updated_at'=>'2017-11-13 06:09:31',
            'deleted_at'=>NULL
        ] );



        CarteraServicios::create( [
            'id'=>10,
            'nombre'=>'Área de Expulsión',
            'created_at'=>'2017-11-13 06:10:06',
            'updated_at'=>'2017-11-13 06:10:06',
            'deleted_at'=>NULL
        ] );



        CarteraServicios::create( [
            'id'=>11,
            'nombre'=>'Área Quirúrgica',
            'created_at'=>'2017-11-13 06:10:32',
            'updated_at'=>'2017-11-13 06:10:32',
            'deleted_at'=>NULL
        ] );



        CarteraServicios::create( [
            'id'=>12,
            'nombre'=>'Tratamiento de hemorragia obstetricia',
            'created_at'=>'2017-11-13 06:10:58',
            'updated_at'=>'2017-11-13 06:10:58',
            'deleted_at'=>NULL
        ] );



        CarteraServicios::create( [
            'id'=>13,
            'nombre'=>'Terapia Intensiva Neonatal',
            'created_at'=>'2017-11-13 06:11:19',
            'updated_at'=>'2017-11-13 06:11:19',
            'deleted_at'=>NULL
        ] );
    }
}
