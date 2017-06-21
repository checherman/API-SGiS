<?php

namespace App;

namespace App\Models\Catalogos;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GruposCie10 extends Model
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "grupos_cie10";
    protected $fillable = ["id","nombre"];

    public function categoriasCie10()
    {
        return $this->hasMany(CategoriasCie10::class,'grupos_cie10_id','id')->with('subCategoriasCie10');
    }
}
