<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class TiposNotificaciones extends BaseModel
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "tipos_notificaciones";
    protected $fillable = ["id", "nombre", "descripcion"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function escalamientos_notificaciones(){
        return $this->hasMany(EscalamientosNotificaciones::class);
    }
}
