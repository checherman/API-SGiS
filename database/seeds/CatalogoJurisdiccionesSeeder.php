<?php

use App\Models\Catalogos\Jurisdicciones;
use Illuminate\Database\Seeder;

class CatalogoJurisdiccionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Jurisdicciones::create( [
            'id'=>1,
            'clave'=>'01',
            'nombre'=>'TUXTLA GUTIÉRREZ',
            'entidades_id'=>7
        ] );



        Jurisdicciones::create( [
            'id'=>2,
            'clave'=>'02',
            'nombre'=>'SAN CRISTÓBAL DE LAS CASAS',
            'entidades_id'=>7
        ] );



        Jurisdicciones::create( [
            'id'=>3,
            'clave'=>'03',
            'nombre'=>'COMITÁN',
            'entidades_id'=>7
        ] );



        Jurisdicciones::create( [
            'id'=>4,
            'clave'=>'04',
            'nombre'=>'VILLAFLORES',
            'entidades_id'=>7
        ] );



        Jurisdicciones::create( [
            'id'=>5,
            'clave'=>'05',
            'nombre'=>'PICHUCALCO',
            'entidades_id'=>7
        ] );



        Jurisdicciones::create( [
            'id'=>6,
            'clave'=>'06',
            'nombre'=>'PALENQUE',
            'entidades_id'=>7
        ] );



        Jurisdicciones::create( [
            'id'=>7,
            'clave'=>'07',
            'nombre'=>'TAPACHULA',
            'entidades_id'=>7
        ] );



        Jurisdicciones::create( [
            'id'=>8,
            'clave'=>'08',
            'nombre'=>'TONALÁ',
            'entidades_id'=>7
        ] );



        Jurisdicciones::create( [
            'id'=>9,
            'clave'=>'09',
            'nombre'=>'OCOSINGO',
            'entidades_id'=>7
        ] );



        Jurisdicciones::create( [
            'id'=>10,
            'clave'=>'10',
            'nombre'=>'MOTOZINTLA',
            'entidades_id'=>7
        ] );
    }
}
