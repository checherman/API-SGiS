<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\Transacciones\Incidencias;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EstadosIncidencias extends Model
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "estados_incidencias";
    protected $fillable = ["id","nombre","descripcion"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function incidencias()
    {
        return $this->hasMany(Incidencias::class);
    }
}
