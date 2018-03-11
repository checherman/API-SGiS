<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\BaseModel;
use App\Models\Sistema\SisUsuario;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cargo extends BaseModel
{
    use SoftDeletes;

    protected $table = "cargos";
    protected $fillable = ["id", "nombre", "descripcion"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];


    public function SisUsuario()
    {
        return $this->hasMany(SisUsuario::class);
    }
}
