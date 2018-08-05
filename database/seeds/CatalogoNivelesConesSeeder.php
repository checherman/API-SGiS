<?php

use App\Models\Catalogos\NivelesCones;
use Illuminate\Database\Seeder;

class CatalogoNivelesConesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        NivelesCones::create( [
            'id'=>1,
            'nombre'=>'Basico',
            'created_at'=>'2017-11-01 15:03:45',
            'updated_at'=>'2017-11-01 15:03:50',
            'deleted_at'=>NULL
        ] );



        NivelesCones::create( [
            'id'=>2,
            'nombre'=>'Completo',
            'created_at'=>'2017-11-01 15:03:47',
            'updated_at'=>'2017-11-01 15:03:52',
            'deleted_at'=>NULL
        ] );    }
}
