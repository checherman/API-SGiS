<?php

use App\Models\Catalogos\Cargo;
use Illuminate\Database\Seeder;

class CatalogoCargosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cargo::create( [
            'id'=>1,
            'nombre'=>'Enfermería',
            'descripcion'=>'Enfermería',
            'created_at'=>'2017-10-17 19:07:58',
            'updated_at'=>'2018-02-26 02:42:37',
            'deleted_at'=>NULL
        ] );


        Cargo::create( [
            'id'=>2,
            'nombre'=>'Médico General de Unidad',
            'descripcion'=>'Médico de unidad',
            'created_at'=>'2017-11-19 23:18:39',
            'updated_at'=>'2017-11-19 23:29:13',
            'deleted_at'=>NULL
        ] );



        Cargo::create( [
            'id'=>3,
            'nombre'=>'Médico',
            'descripcion'=>'Médico de Unidad',
            'created_at'=>'2017-11-24 06:23:41',
            'updated_at'=>'2017-11-24 06:23:41',
            'deleted_at'=>NULL
        ] );



        Cargo::create( [
            'id'=>4,
            'nombre'=>'Director',
            'descripcion'=>'Director de la Unidad',
            'created_at'=>'2018-02-16 11:34:55',
            'updated_at'=>'2018-02-16 11:34:57',
            'deleted_at'=>NULL
        ] );



        Cargo::create( [
            'id'=>5,
            'nombre'=>'Trabajo Social',
            'descripcion'=>'Trabajo Social de la Unidad',
            'created_at'=>'2018-02-16 11:35:18',
            'updated_at'=>'2018-02-16 11:35:20',
            'deleted_at'=>NULL
        ] );



        Cargo::create( [
            'id'=>6,
            'nombre'=>'Coordinadora Trabajo Social',
            'descripcion'=>NULL,
            'created_at'=>'2018-02-19 22:52:01',
            'updated_at'=>'2018-02-19 22:52:04',
            'deleted_at'=>NULL
        ] );



        Cargo::create( [
            'id'=>7,
            'nombre'=>'Area de Ingreso',
            'descripcion'=>NULL,
            'created_at'=>'2018-02-19 22:54:03',
            'updated_at'=>'2018-02-19 22:54:06',
            'deleted_at'=>NULL
        ] );



        Cargo::create( [
            'id'=>8,
            'nombre'=>'Jefe Juridisdiccional',
            'descripcion'=>'Jefe de Jurisdicción ISECH',
            'created_at'=>'2018-02-20 18:57:42',
            'updated_at'=>'2018-02-20 18:59:26',
            'deleted_at'=>NULL
        ] );


        Cargo::create( [
            'id'=>9,
            'nombre'=>'Enlace Jurisdiccional',
            'descripcion'=>'Enlace',
            'created_at'=>'2018-02-20 18:57:59',
            'updated_at'=>'2018-02-20 18:57:59',
            'deleted_at'=>NULL
        ] );


        Cargo::create( [
            'id'=>10,
            'nombre'=>'Responsable de Casa Materna',
            'descripcion'=>'Responsable de Casa Materna',
            'created_at'=>'2018-02-21 21:08:54',
            'updated_at'=>'2018-02-21 21:08:54',
            'deleted_at'=>NULL
        ] );


        Cargo::create( [
            'id'=>11,
            'nombre'=>'Psicólogía Clínica',
            'descripcion'=>'Psicología Clínica',
            'created_at'=>'2018-02-21 21:16:48',
            'updated_at'=>'2018-02-21 21:16:48',
            'deleted_at'=>NULL
        ] );


        Cargo::create( [
            'id'=>12,
            'nombre'=>'Area Administrativa',
            'descripcion'=>'Area Administrativa',
            'created_at'=>'2018-02-21 21:21:08',
            'updated_at'=>'2018-02-21 21:21:08',
            'deleted_at'=>NULL
        ] );


        Cargo::create( [
            'id'=>13,
            'nombre'=>'Area de Estadística',
            'descripcion'=>'Area Administrativa de Estadística',
            'created_at'=>'2018-02-26 02:19:25',
            'updated_at'=>'2018-02-26 02:19:46',
            'deleted_at'=>NULL
        ] );


        Cargo::create( [
            'id'=>14,
            'nombre'=>'Radiología',
            'descripcion'=>'Radiología',
            'created_at'=>'2018-02-26 02:36:13',
            'updated_at'=>'2018-02-26 02:36:13',
            'deleted_at'=>NULL
        ] );


        Cargo::create( [
            'id'=>15,
            'nombre'=>'Técnico Laboratorista',
            'descripcion'=>'Técnico Laboratorista',
            'created_at'=>'2018-02-26 02:56:14',
            'updated_at'=>'2018-02-26 19:11:58',
            'deleted_at'=>NULL
        ] );


        Cargo::create( [
            'id'=>16,
            'nombre'=>'Area de Química',
            'descripcion'=>'Area de Química',
            'created_at'=>'2018-02-26 03:08:51',
            'updated_at'=>'2018-02-26 03:08:51',
            'deleted_at'=>NULL
        ] );


        Cargo::create( [
            'id'=>17,
            'nombre'=>'Nutrición',
            'descripcion'=>'Nutrición',
            'created_at'=>'2018-02-26 03:12:45',
            'updated_at'=>'2018-02-26 03:12:45',
            'deleted_at'=>NULL
        ] );


        Cargo::create( [
            'id'=>18,
            'nombre'=>'Farmacia',
            'descripcion'=>'Farmacia',
            'created_at'=>'2018-02-26 04:08:06',
            'updated_at'=>'2018-02-26 04:08:06',
            'deleted_at'=>NULL
        ] );
    }
}
