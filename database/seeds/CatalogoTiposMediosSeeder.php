<?php

use App\Models\Catalogos\TiposMedios;
use Illuminate\Database\Seeder;

class CatalogoTiposMediosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TiposMedios::create( [
            'id'=>1,
            'nombre'=>'Correo',
            'created_at'=>'2017-11-07 13:52:41',
            'updated_at'=>'2017-11-07 13:52:44',
            'deleted_at'=>NULL,
            'icono'=>NULL
        ] );



        TiposMedios::create( [
            'id'=>2,
            'nombre'=>'Celular',
            'created_at'=>'2017-12-01 16:02:41',
            'updated_at'=>'2017-12-01 16:02:41',
            'deleted_at'=>NULL,
            'icono'=>NULL
        ] );
    }
}
