<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriasCie10 extends BaseModel
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "categorias_cie10";
    protected $fillable = ["id","codigo","nombre", "grupos_cie10_id"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function grupoCie10()
    {
        return $this->belongsTo(GruposCie10::class,'grupos_cie10_id','id');
    }

    public function subCategoriasCie10()
    {
        return $this->hasMany(SubCategoriasCie10::class);
    }
}
