<?php

use App\Models\Catalogos\EstadosPacientes;
use Illuminate\Database\Seeder;

class SisModulosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();
        $path = storage_path().'/app/seeds/sis_modulos.sql';
        DB::unprepared(file_get_contents($path));

    }
}
