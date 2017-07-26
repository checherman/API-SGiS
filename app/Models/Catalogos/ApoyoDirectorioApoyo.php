<?php

namespace App;

namespace App\Models\Catalogos;
use Illuminate\Database\Eloquent\Model;

class ApoyoDirectorioApoyo extends Model
{
    protected $table = "apoyo_directorio_apoyo";
    protected $fillable = ["directorio_apoyos_id", "apoyos_id"];

    public function directorioApoyo()
    {
        return $this->belongsTo(DirectorioApoyos::class,'directorio_apoyos_id','id')->with("apoyos");
    }
}
