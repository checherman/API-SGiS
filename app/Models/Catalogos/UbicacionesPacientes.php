<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class UbicacionesPacientes extends BaseModel
{
    protected $table = "ubicaciones_pacientes";
    protected $fillable = ["id","nombre","descripcion"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function base_conocimientos()
    {
        return $this->hasMany(UbicacionesPacientes::class);
    }

    public function movimientos_incidencias()
    {
        return $this->hasMany(MovimientosIncidencias::class);
    }
}