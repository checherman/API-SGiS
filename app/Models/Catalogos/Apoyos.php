<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Apoyos extends BaseModel
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "apoyos";
    protected $fillable = ["id", "nombre", "descripcion"];

    public function directorio(){
        return $this->belongsToMany(DirectorioApoyos::class, 'apoyo_directorio_apoyo', 'apoyos_id', 'directorio_apoyos_id');
    }
}
