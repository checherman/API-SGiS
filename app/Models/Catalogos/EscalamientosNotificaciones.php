<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\BaseModel;
use App\Models\Sistema\SisUsuario;
use Illuminate\Database\Eloquent\SoftDeletes;

class EscalamientosNotificaciones extends BaseModel
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "escalamientos_notificaciones";
    protected $fillable = ["id", "usuarios_id", "tipos_notificaciones_id"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function tipos_notificaciones(){
        return $this->belongsTo(TiposNotificaciones::class);
    }

    public function usuarios(){
        return $this->belongsTo(SisUsuario::class);
    }
}
