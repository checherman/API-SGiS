<?php

use App\Models\Catalogos\TiposItems;
use Illuminate\Database\Seeder;

class CatalogoTiposItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TiposItems::create( [
            'id'=>1,
            'nombre'=>'Si / No',
            'descripcion'=>NULL,
            'created_at'=>'2017-08-07 13:21:32',
            'updated_at'=>'2017-08-07 13:21:35',
            'deleted_at'=>NULL
        ] );

        TiposItems::create( [
            'id'=>2,
            'nombre'=>'Numerico',
            'descripcion'=>NULL,
            'created_at'=>'2017-08-07 13:21:52',
            'updated_at'=>'2017-08-07 13:21:54',
            'deleted_at'=>NULL
        ] );

        TiposItems::create( [
            'id'=>3,
            'nombre'=>'numero',
            'descripcion'=>'aaa',
            'created_at'=>'2017-10-13 18:09:21',
            'updated_at'=>'2017-10-13 18:09:38',
            'deleted_at'=>'2017-10-13 18:09:38'
        ] );

        TiposItems::create( [
            'id'=>4,
            'nombre'=>'Texto',
            'descripcion'=>NULL,
            'created_at'=>'2017-10-17 18:46:19',
            'updated_at'=>'2017-10-17 18:46:29',
            'deleted_at'=>'2017-10-17 18:46:29'
        ] );

    }
}
