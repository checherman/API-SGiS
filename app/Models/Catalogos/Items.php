<?php

namespace App;

namespace App\Models\Catalogos;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Items extends Model
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "items";
    protected $fillable = ["id", "nombre", "cartera_servicios_id"];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function carteraServicio()
    {
        return $this->belongsTo(CarteraServicios::class);
    }

    public function tipoItem()
    {
        return $this->belongsTo(TiposItems::class);
    }
}
