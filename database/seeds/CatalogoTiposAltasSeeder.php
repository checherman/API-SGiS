<?php

use App\Models\Catalogos\TiposAltas;
use Illuminate\Database\Seeder;

class CatalogoTiposAltasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TiposAltas::create( [
            'id'=>1,
            'nombre'=>'Alta por mejoria',
            'descripcion'=>'..',
            'created_at'=>'2017-11-11 22:54:13',
            'updated_at'=>'2017-11-13 18:33:50',
            'deleted_at'=>NULL
        ] );



        TiposAltas::create( [
            'id'=>2,
            'nombre'=>'Alta voluntaria',
            'descripcion'=>NULL,
            'created_at'=>'2017-11-11 22:54:27',
            'updated_at'=>'2017-11-11 22:54:29',
            'deleted_at'=>NULL
        ] );



        TiposAltas::create( [
            'id'=>3,
            'nombre'=>'Alta por defunsion',
            'descripcion'=>NULL,
            'created_at'=>'2017-11-11 22:54:39',
            'updated_at'=>'2017-11-11 22:54:42',
            'deleted_at'=>NULL
        ] );
    }
}
