<?php

use App\Models\Catalogos\Parentesco;
use Illuminate\Database\Seeder;

class CatalogoParentescosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Parentesco::create( [
            'id'=>1,
            'nombre'=>'Esposo',
            'created_at'=>'2017-08-07 17:36:45',
            'updated_at'=>'2017-08-07 17:36:45',
            'deleted_at'=>NULL
        ] );



        Parentesco::create( [
            'id'=>2,
            'nombre'=>'Papá',
            'created_at'=>'2017-08-07 17:36:59',
            'updated_at'=>'2017-08-07 17:36:59',
            'deleted_at'=>NULL
        ] );



        Parentesco::create( [
            'id'=>3,
            'nombre'=>'Mamá',
            'created_at'=>'2017-08-07 17:37:08',
            'updated_at'=>'2017-10-18 21:25:28',
            'deleted_at'=>NULL
        ] );



        Parentesco::create( [
            'id'=>4,
            'nombre'=>'Hijo',
            'created_at'=>'2017-11-18 02:10:20',
            'updated_at'=>'2017-11-18 02:10:22',
            'deleted_at'=>NULL
        ] );



        Parentesco::create( [
            'id'=>5,
            'nombre'=>'Hija',
            'created_at'=>'2017-11-18 02:10:25',
            'updated_at'=>'2017-11-18 02:10:27',
            'deleted_at'=>NULL
        ] );



        Parentesco::create( [
            'id'=>6,
            'nombre'=>'Yerno',
            'created_at'=>'2017-11-18 02:10:31',
            'updated_at'=>'2017-11-18 02:10:33',
            'deleted_at'=>NULL
        ] );



        Parentesco::create( [
            'id'=>7,
            'nombre'=>'Nuera',
            'created_at'=>'2017-11-18 02:10:35',
            'updated_at'=>'2017-11-18 02:10:38',
            'deleted_at'=>NULL
        ] );



        Parentesco::create( [
            'id'=>8,
            'nombre'=>'Suegro',
            'created_at'=>'2017-11-18 02:10:40',
            'updated_at'=>'2017-11-18 02:10:43',
            'deleted_at'=>NULL
        ] );



        Parentesco::create( [
            'id'=>9,
            'nombre'=>'Suegra',
            'created_at'=>'2017-11-18 02:10:46',
            'updated_at'=>'2017-11-18 02:10:50',
            'deleted_at'=>NULL
        ] );



        Parentesco::create( [
            'id'=>10,
            'nombre'=>'Abuelo',
            'created_at'=>'2017-11-18 02:10:52',
            'updated_at'=>'2017-11-18 02:10:55',
            'deleted_at'=>NULL
        ] );



        Parentesco::create( [
            'id'=>11,
            'nombre'=>'Abuela',
            'created_at'=>'2017-11-18 02:10:58',
            'updated_at'=>'2017-11-18 02:11:00',
            'deleted_at'=>NULL
        ] );



        Parentesco::create( [
            'id'=>12,
            'nombre'=>'Hermano',
            'created_at'=>'2017-11-18 02:11:02',
            'updated_at'=>'2017-11-18 02:11:05',
            'deleted_at'=>NULL
        ] );



        Parentesco::create( [
            'id'=>13,
            'nombre'=>'Hermana',
            'created_at'=>'2017-11-18 02:11:07',
            'updated_at'=>'2017-11-18 02:11:09',
            'deleted_at'=>NULL
        ] );



        Parentesco::create( [
            'id'=>14,
            'nombre'=>'Otros',
            'created_at'=>'2017-11-18 02:11:17',
            'updated_at'=>'2017-11-18 02:11:21',
            'deleted_at'=>NULL
        ] );
    }
}
