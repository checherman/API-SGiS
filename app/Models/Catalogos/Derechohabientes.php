<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\BaseModel;
use App\Models\Transacciones\Personas;
use Illuminate\Database\Eloquent\SoftDeletes;

class Derechohabientes extends BaseModel
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "derechohabientes";
    protected $fillable = ["id","nombre","descripcion"];

    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function personas()
    {
        return $this->hasMany(Personas::class);
    }

}
