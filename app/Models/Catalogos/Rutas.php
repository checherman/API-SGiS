<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rutas extends BaseModel
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "rutas";
    protected $fillable = ["id","nombre", "clues_origen", "clues_destino", "tiempo_traslado", "distancia_traslado", "observaciones", "numeroLatitud_origen", "numeroLongitud_origen", "numeroLatitud_destino", "numeroLongitud_destino"];
}
