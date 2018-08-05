<?php

use App\Models\Catalogos\Clues;
use Illuminate\Database\Seeder;

class CatalogoCluesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('clues')->insert([
            'clues'=>'CSSSA005773',
            'abreviacion'=>'H. Mujer SCLC',
            'nombre'=>'HOSPITAL DE LA MUJER SAN CRISTÓBAL DE LAS CASAS',
            'domicilio'=>'AV. Insurgentes # 24, Barrio de Santa Lucia, entr Dr. Ramon Corona y Julio M. Corzo',
            'codigoPostal'=>29200,
            'numeroLongitud'=>'-92.637003',
            'numeroLatitud'=>16.733055,
            'entidad'=>'CHIAPAS',
            'estado'=>'EN OPERACIÓN',
            'institucion'=>'SECRETARÍA DE SALUD',
            'jurisdicciones_id'=>2,
            'localidad'=>'San Cristóbal de las Casas',
            'municipios_id'=>78,
            'tipologia'=>'HOSPITAL GENERAL',
            'nivel_cone_id'=>2,
            'activo'=>1
        ] );



        DB::table('clues')->insert([
            'clues'=>'CSSSA007540',
            'abreviacion'=>'H. Pascasio TGZ',
            'nombre'=>'HOSPITAL REGIONAL DR. RAFAEL PASCASIO GAMBOA TUXTLA',
            'domicilio'=>'9a. Sur esq. calle Central S/N, Col. San Francisco',
            'codigoPostal'=>29000,
            'numeroLongitud'=>'-93.116982',
            'numeroLatitud'=>16.745821,
            'entidad'=>'CHIAPAS',
            'estado'=>'EN OPERACIÓN',
            'institucion'=>'SECRETARÍA DE SALUD',
            'jurisdicciones_id'=>1,
            'localidad'=>'Tuxtla Gutiérrez',
            'municipios_id'=>101,
            'tipologia'=>'HOSPITAL GENERAL',
            'nivel_cone_id'=>2,
            'activo'=>1
        ] );



        DB::table('clues')->insert([
            'clues'=>'CSSSA019645',
            'abreviacion'=>NULL,
            'nombre'=>'H. B. C. DE OCOSINGO',
            'domicilio'=>'Carretera Ocosingo ruinas de Tonina Km 2, Barrio Santo Tomas',
            'codigoPostal'=>29950,
            'numeroLongitud'=>'-92.072545',
            'numeroLatitud'=>16.896122,
            'entidad'=>'CHIAPAS',
            'estado'=>'EN OPERACIÓN',
            'institucion'=>'SECRETARÍA DE SALUD',
            'jurisdicciones_id'=>9,
            'localidad'=>'Ocosingo',
            'municipios_id'=>59,
            'tipologia'=>'HOSPITAL INTEGRAL (COMUNITARIO)',
            'nivel_cone_id'=>2,
            'activo'=>1
        ] );
    }
}
