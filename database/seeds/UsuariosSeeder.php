<?php

use App\Models\Sistema\SisUsuario;
use Illuminate\Database\Seeder;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SisUsuario::create( [
            'id'=>1,
            'nombre'=>'Usuario Root',
            'username'=>'root',
            'email'=>'usuario.root@gmail.com',
            'password'=>'$2y$10$GlMOakF7y8fi/226/vWtjuX4oJKCnDVkoafWx925RHuPLQ01Am2V.',
            'direccion'=>NULL,
            'numero_exterior'=>NULL,
            'numero_interior'=>NULL,
            'colonia'=>NULL,
            'codigo_postal'=>NULL,
            'comentario'=>'',
            'foto'=>'HTTP/1.0 400 Bad Request\r\nCache-Control: no-cache, private\r\nContent-Type:  application/json\r\n\r\n{\"error\":\"imagecreatefromstring(): Data is not in a recognized format\",\"nombre\":\"usuario\"}',
            'spam'=>NULL,
            'localidades_id'=>1,
            'estados_id'=>7,
            'municipios_id'=>1,
            'es_super'=>1,
            'activo'=>1,
            'avatar'=>'https://www.menon.no/wp-content/uploads/person-placeholder.jpg',
            'reset_password_code'=>NULL,
            'persist_code'=>NULL,
            'last_login'=>NULL,
            'activated_at'=>NULL,
            'activation_code'=>NULL,
            'activated'=>0,
            'permisos'=>NULL,
            'remember_token'=>'',
            'created_at'=>'2015-11-16 00:00:00',
            'updated_at'=>'2017-11-01 22:42:45',
            'deleted_at'=>NULL,
            'creado_por'=>0,
            'modificado_por'=>1,
            'borrado_por'=>0,
            'cargos_id'=>12
        ] );
    }
}
