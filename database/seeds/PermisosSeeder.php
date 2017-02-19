<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permisos')->insert([
            [
                'id' => str_random(32),//str_random(32)
                'descripcion' => "Ver usuarios",
                'grupo' => "Administrador",     
                'su' => false,           
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => str_random(32),
                'descripcion' => "Agregar usuarios",
                'grupo' => "Administrador",
                'su' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => str_random(32),
                'descripcion' => "Editar usuarios",
                'grupo' => "Administrador",
                'su' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => str_random(32),
                'descripcion' => "Eliminar usuarios",
                'grupo' => "Administrador",
                'su' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => str_random(32),
                'descripcion' => "Ver roles",
                'grupo' => "Administrador",
                'su' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => str_random(32),
                'descripcion' => "Agregar roles",
                'grupo' => "Administrador",
                'su' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => str_random(32),
                'descripcion' => "Editar roles",
                'grupo' => "Administrador",
                'su' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => str_random(32),
                'descripcion' => "Eliminar roles",
                'grupo' => "Administrador",
                'su' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => str_random(32),
                'descripcion' => "Ver permisos",
                'grupo' => "Super Usuario",
                'su' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => str_random(32),
                'descripcion' => "Agregar permisos",
                'grupo' => "Super Usuario",
                'su' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => str_random(32),
                'descripcion' => "Editar permisos",
                'grupo' => "Super Usuario",
                'su' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => str_random(32),
                'descripcion' => "Eliminar permisos",
                'grupo' => "Super Usuario",
                'su' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
