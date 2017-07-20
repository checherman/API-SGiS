<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class DirectorioApoyos extends BaseModel
{
    use SoftDeletes;

    protected $generarID = true;
    protected $guardarIDServidor = true;
    protected $guardarIDUsuario = false;
    public $incrementing = false;

    protected $table = "directorio_apoyos";
    protected $fillable = ["id", "servidor_id", "institucion", "direccion", "responsable", "telefono", "correo", "municipios_id"];

    public function apoyos()
    {
        return $this->belongsToMany(Apoyos::class, 'apoyo_directorio_apoyo', 'directorio_apoyos_id', 'apoyos_id');
    }
}
