<?php

use App\Models\Catalogos\EstadosEmbarazos;
use Illuminate\Database\Seeder;

class CatalogoEstadosEmbarazoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EstadosEmbarazos::create( [
            'id'=>1,
            'nombre'=>'Embarazo con riesgo',
            'descripcion'=>NULL,
            'created_at'=>'2017-08-07 17:36:45',
            'updated_at'=>'2017-08-07 17:36:45',
            'deleted_at'=>NULL
        ] );



        EstadosEmbarazos::create( [
            'id'=>2,
            'nombre'=>'Embarazo sin riesgo',
            'descripcion'=>NULL,
            'created_at'=>'2017-08-07 17:36:59',
            'updated_at'=>'2017-08-07 17:36:59',
            'deleted_at'=>NULL
        ] );



        EstadosEmbarazos::create( [
            'id'=>3,
            'nombre'=>'Sin embarazo',
            'descripcion'=>NULL,
            'created_at'=>'2017-08-07 17:37:08',
            'updated_at'=>'2017-08-07 17:37:08',
            'deleted_at'=>NULL
        ] );
    }
}
