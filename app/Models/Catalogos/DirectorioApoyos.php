<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class
DirectorioApoyos extends BaseModel
{
    protected $table = "directorio_apoyos";
    protected $fillable = ["id", "institucion", "direccion", "responsable", "telefono", "correo", "municipios_id", "localidades_id"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function apoyos()
    {
        return $this->belongsToMany(Apoyos::class, 'apoyo_directorio_apoyo', 'directorio_apoyos_id', 'apoyos_id');
    }
}
