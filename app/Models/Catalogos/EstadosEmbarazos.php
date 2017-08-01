<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EstadosEmbarazos extends BaseModel
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "estados_embarazos";
    protected $fillable = ["id","nombre","descripcion"];

    protected $hidden = ["created_at", "updated_at", "deleted_at"];

}
