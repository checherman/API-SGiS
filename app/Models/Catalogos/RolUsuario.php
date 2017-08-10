<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\Sistema\Usuario;
use Illuminate\Database\Eloquent\Model;

class RolUsuario extends Model
{
    protected $table = "rol_usuario";
    protected $fillable = ["rol_id", "usuario_id"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class,'usuario_id','id')->with("roles");
    }
}
