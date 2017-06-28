<?php

namespace App;

namespace App\Models\Transacciones;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends BaseModel
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "personas";
    protected $fillable = ["id", "servidor_id", "nombre", "paterno", "materno", "fecha_nacimiento", "telefono"];
}
