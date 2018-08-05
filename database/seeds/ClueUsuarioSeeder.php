<?php

use App\Models\Sistema\SisUsuariosClues;
use Illuminate\Database\Seeder;

class ClueUsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('clue_usuario')->insert([
            'sis_usuarios_id'=>1,
            'clues'=>'CSSSA005773'
        ] );

        DB::table('clue_usuario')->insert([
            'sis_usuarios_id'=>1,
            'clues'=>'CSSSA019645'
        ] );
    }
}
