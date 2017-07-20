<?php

namespace App;

namespace App\Models\Catalogos;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategoriasCie10 extends Model
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "subcategorias_cie10";
    protected $fillable = ["id","nombre", "categorias_cie10_id"];

    public function categoriaCie10()
    {
        return $this->belongsTo(CategoriasCie10::class, 'categorias_cie10_id','id');
    }

    public function baseConocimiento()
    {
        return $this->hasMany(SubCategoriasCie10::class);
    }
}
