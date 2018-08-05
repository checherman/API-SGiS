<?php

use App\Models\Catalogos\MetodoPlanificacion;
use Illuminate\Database\Seeder;

class CatalogoMetodosPlanificacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MetodoPlanificacion::create( [
            'id'=>1,
            'nombre'=>'T de Cobre',
            'descripcion'=>NULL,
            'created_at'=>'2017-10-13 19:15:53',
            'updated_at'=>'2017-10-13 19:15:53',
            'deleted_at'=>NULL
        ] );



        MetodoPlanificacion::create( [
            'id'=>2,
            'nombre'=>'Implante',
            'descripcion'=>NULL,
            'created_at'=>'2017-10-18 19:19:00',
            'updated_at'=>'2017-10-18 19:19:00',
            'deleted_at'=>NULL
        ] );



        MetodoPlanificacion::create( [
            'id'=>3,
            'nombre'=>'Dispositivo Mirena',
            'descripcion'=>NULL,
            'created_at'=>'2017-11-28 17:00:07',
            'updated_at'=>'2017-11-28 17:00:07',
            'deleted_at'=>NULL
        ] );



        MetodoPlanificacion::create( [
            'id'=>4,
            'nombre'=>'Oclusión Tubaria Bilateral (OTB)',
            'descripcion'=>NULL,
            'created_at'=>'2017-11-28 17:01:01',
            'updated_at'=>'2017-11-28 17:01:01',
            'deleted_at'=>NULL
        ] );



        MetodoPlanificacion::create( [
            'id'=>5,
            'nombre'=>'No acepto metodo',
            'descripcion'=>NULL,
            'created_at'=>'2017-11-29 16:19:09',
            'updated_at'=>'2017-11-29 16:19:09',
            'deleted_at'=>NULL
        ] );

    }
}
