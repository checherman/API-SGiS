<?php

use App\Models\Catalogos\TiposNotificaciones;
use Illuminate\Database\Seeder;

class CatalogoTiposNotificacionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TiposNotificaciones::create( [
            'id'=>2,
            'nombre'=>'Seguimiento',
            'descripcion'=>'Un paciente se le realiza una atención.',
            'created_at'=>'2017-10-18 21:28:02',
            'updated_at'=>'2017-11-06 20:15:31',
            'deleted_at'=>NULL
        ] );



        TiposNotificaciones::create( [
            'id'=>3,
            'nombre'=>'Referencia',
            'descripcion'=>'El paciente fue referido.',
            'created_at'=>'2017-11-06 20:16:12',
            'updated_at'=>'2017-11-06 20:16:12',
            'deleted_at'=>NULL
        ] );



        TiposNotificaciones::create( [
            'id'=>1,
            'nombre'=>'Nuevo Ingreso',
            'descripcion'=>'Manda notificación cuando ingresa un paciente.',
            'created_at'=>'2017-10-16 23:20:14',
            'updated_at'=>'2017-11-06 20:11:05',
            'deleted_at'=>NULL
        ] );



        TiposNotificaciones::create( [
            'id'=>4,
            'nombre'=>'Alta Paciente',
            'descripcion'=>'El paciente fue dado de alta.',
            'created_at'=>'2017-11-06 20:16:33',
            'updated_at'=>'2017-11-06 20:16:33',
            'deleted_at'=>NULL
        ] );


    }
}
