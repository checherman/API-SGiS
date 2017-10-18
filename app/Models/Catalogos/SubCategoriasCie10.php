<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\BaseModel;
use App\Models\Transacciones\MovimientosIncidencias;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategoriasCie10 extends BaseModel
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "subcategorias_cie10";
    protected $fillable = ["id","nombre","codigo", "categorias_cie10_id"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function categoriaCie10()
    {
        return $this->belongsTo(CategoriasCie10::class, 'categorias_cie10_id','id');
    }

    public function baseConocimiento()
    {
        return $this->hasMany(SubCategoriasCie10::class);
    }

    public function movimientos_incidencias()
    {
        return $this->hasMany(MovimientosIncidencias::class);
    }
}
