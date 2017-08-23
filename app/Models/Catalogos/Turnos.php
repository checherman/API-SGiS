<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\Transacciones\MovimientosIncidencias;
use App\Models\Transacciones\RespuestasEstadosFuerza;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Turnos extends Model
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "turnos";
    protected $fillable = ["id", "nombre", "descripcion"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function movimientos_incidencias()
    {
        return $this->hasMany(MovimientosIncidencias::class);
    }

    public function respuestas_estados_fuerza()
    {
        return $this->hasMany(RespuestasEstadosFuerza::class);
    }
}
