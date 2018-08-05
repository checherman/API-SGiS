<?php

use App\Models\Catalogos\Apoyos;
use Illuminate\Database\Seeder;

class CatalogoApoyosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Apoyos::create( [
            'id'=>1,
            'nombre'=>'Ambulancia',
            'descripcion'=>'para traslado de embarazadas',
            'created_at'=>'2017-10-13 17:50:28',
            'updated_at'=>'2017-10-13 17:55:28',
            'deleted_at'=>NULL
        ] );

        Apoyos::create( [
            'id'=>2,
            'nombre'=>'Camillas',
            'descripcion'=>'de palacio municipal.',
            'created_at'=>'2017-10-13 17:53:59',
            'updated_at'=>'2017-10-13 17:55:09',
            'deleted_at'=>NULL
        ] );
    }
}
