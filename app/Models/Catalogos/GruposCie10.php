<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class GruposCie10 extends BaseModel
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "grupos_cie10";
    protected $fillable = ["id","codigo","nombre"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function categoriasCie10()
    {
        return $this->hasMany(CategoriasCie10::class,'grupos_cie10_id','id')->with('SubCategoriasCie10');
    }
}
