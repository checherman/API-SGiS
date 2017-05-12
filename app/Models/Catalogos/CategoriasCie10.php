<?php

namespace App;

namespace App\Models\Catalogos;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriasCie10 extends Model
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "categorias_cie10";
    protected $fillable = ["id","nombre", "grupos_cie10_id"];

    public function grupoCie10()
    {
        return $this->belongsTo(GruposCie10::class,'grupos_cie10_id','id');
    }

    public function subCategoriasCie10()
    {
        return $this->hasMany(SubCategoriasCie10::class);
    }
}
