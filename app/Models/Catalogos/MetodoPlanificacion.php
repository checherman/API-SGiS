<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\BaseModel;
use App\Models\Transacciones\AltasIncidencias;
use Illuminate\Database\Eloquent\SoftDeletes;

class MetodoPlanificacion extends BaseModel
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "metodos_planificacion";
    protected $fillable = ["id","nombre","descripcion"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function altas_incidencias()
    {
        return $this->hasMany(AltasIncidencias::class);
    }

}
