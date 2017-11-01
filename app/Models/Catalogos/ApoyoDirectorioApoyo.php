<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\BaseModel;

class ApoyoDirectorioApoyo extends BaseModel
{
    protected $table = "apoyo_directorio_apoyo";
    protected $fillable = ["directorio_apoyos_id", "apoyos_id"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function directorioApoyo()
    {
        return $this->belongsTo(DirectorioApoyos::class,'directorio_apoyos_id','id')->with("apoyos");
    }
}
