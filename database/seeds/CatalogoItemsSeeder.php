<?php

use Illuminate\Database\Seeder;

class CatalogoItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();
        $path = storage_path().'/app/seeds/items.sql';
        DB::unprepared(file_get_contents($path));
    }
}
