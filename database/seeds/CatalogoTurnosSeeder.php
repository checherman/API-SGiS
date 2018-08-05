<?php

use App\Models\Catalogos\Turnos;
use Illuminate\Database\Seeder;

class CatalogoTurnosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Turnos::create( [
            'id'=>1,
            'nombre'=>'Matutino',
            'descripcion'=>'',
            'created_at'=>'2017-08-07 17:36:45',
            'updated_at'=>'2017-08-07 17:36:45',
            'deleted_at'=>NULL
        ] );

        Turnos::create( [
            'id'=>2,
            'nombre'=>'Vespertino',
            'descripcion'=>'',
            'created_at'=>'2017-08-07 17:36:59',
            'updated_at'=>'2017-08-07 17:36:59',
            'deleted_at'=>NULL
        ] );

        Turnos::create( [
            'id'=>3,
            'nombre'=>'Nocturno A',
            'descripcion'=>'Lunes, Miercoles y Viernes',
            'created_at'=>'2017-08-07 17:37:08',
            'updated_at'=>'2017-08-07 17:37:08',
            'deleted_at'=>NULL
        ] );

        Turnos::create( [
            'id'=>4,
            'nombre'=>'Nocturno B',
            'descripcion'=>'Jueves, Martes y Sabado',
            'created_at'=>'2017-08-07 17:36:45',
            'updated_at'=>'2018-06-11 18:33:33',
            'deleted_at'=>NULL
        ] );


        Turnos::create( [
            'id'=>5,
            'nombre'=>'Especial A',
            'descripcion'=>'Domingo y dos dias entre semana',
            'created_at'=>'2017-08-07 17:36:59',
            'updated_at'=>'2017-08-07 17:36:59',
            'deleted_at'=>NULL
        ] );

        Turnos::create( [
            'id'=>6,
            'nombre'=>'Especial B',
            'descripcion'=>'Nocturnos festivos',
            'created_at'=>'2017-08-07 17:37:08',
            'updated_at'=>'2017-08-07 17:37:08',
            'deleted_at'=>NULL
        ] );

        Turnos::create( [
            'id'=>7,
            'nombre'=>'Fin de semana',
            'descripcion'=>'Sabado, Domingo y dias destivos',
            'created_at'=>'2017-08-07 17:37:08',
            'updated_at'=>'2017-08-07 17:37:08',
            'deleted_at'=>NULL
        ] );
    }
}
