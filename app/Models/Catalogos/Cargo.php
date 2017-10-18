<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\BaseModel;
use App\Models\Sistema\Usuario;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cargo extends BaseModel
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "cargos";
    protected $fillable = ["id", "nombre", "descripcion"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function usuario()
    {
        return $this->hasMany(Usuario::class);
    }
}
