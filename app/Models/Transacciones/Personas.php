<?php

namespace App;

namespace App\Models\Transacciones;
use App\Models\BaseModel;
use App\Models\Catalogos\Derechohabientes;
use App\Models\Catalogos\EstadosEmbarazos;
use App\Models\Catalogos\Localidades;
use Illuminate\Database\Eloquent\SoftDeletes;

class Personas extends BaseModel
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = true;
    protected $guardarIDUsuario = false;
    public $incrementing = false;

    protected $table = "personas";
    protected $fillable = ["id", "servidor_id", "nombre", "paterno", "materno", "fecha_nacimiento", "telefono", "domicilio", "estados_embarazos_id", "derechohabientes_id", "localidades_id"];

    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function derechohabientes()
    {
        return $this->belongsTo(Derechohabientes::class);
    }

    public function estados_embarazos()
    {
        return $this->belongsTo(EstadosEmbarazos::class);
    }

    public function localidades()
    {
        return $this->belongsTo(Localidades::class);
    }

    public function pacientes()
    {
        return $this->hasMany(Pacientes::class);
    }

    public function acompaniantes()
    {
        return $this->hasMany(Acompaniantes::class);
    }
}
