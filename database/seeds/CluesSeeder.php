<?php

use Illuminate\Database\Seeder;

class CluesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lista_csv = [
            'clues'                  =>'clues',
        ];

        foreach($lista_csv as $csv => $tabla){
            $archivo_csv = storage_path().'/app/seeds/'.$csv.'.csv';
            $query = sprintf("
                LOAD DATA local INFILE '%s' 
                INTO TABLE $tabla 
                FIELDS TERMINATED BY ',' 
                OPTIONALLY ENCLOSED BY '\"' 
                ESCAPED BY '\"' 
                LINES TERMINATED BY '\\n' 
                IGNORE 1 LINES", addslashes($archivo_csv));
            DB::connection()->getpdo()->exec($query);
        }
    }
}
