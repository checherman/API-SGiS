<?php

use App\Models\Sistema\SisUsuariosContactos;
use Illuminate\Database\Seeder;

class SisUsuariosContactosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SisUsuariosContactos::create( [
            'id'=>1,
            'sis_usuarios_id'=>1,
            'tipos_medios_id'=>1,
            'valor'=>'usuario.root@gmail.com',
            'created_at'=>'2017-10-26 19:29:01',
            'updated_at'=>'2018-06-23 19:01:08',
            'deleted_at'=>NULL,
            'creado_por'=>NULL,
            'modificado_por'=>NULL,
            'borrado_por'=>NULL
        ] );
    }
}
