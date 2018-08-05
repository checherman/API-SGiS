<?php

use Illuminate\Database\Seeder;

class CatalogoGruposCie10Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();
        $path = storage_path().'/app/seeds/grupos_cie10.sql';
        DB::unprepared(file_get_contents($path));
    }
}
