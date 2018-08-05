<?php

use Illuminate\Database\Seeder;

class SisUsuariosGruposSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sis_usuarios_grupos')->insert([
            'sis_usuarios_id'=>1,
            'sis_grupos_id'=>1
        ] );
    }
}
