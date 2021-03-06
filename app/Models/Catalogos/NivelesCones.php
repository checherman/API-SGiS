<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class NivelesCones extends BaseModel
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "niveles_cones";
    protected $fillable = ["id","nombre"];

    protected $hidden = ['pivot'];

    public function carteraServicio()
    {
        return $this->belongsToMany(CarteraServicios::class, 'cartera_servicio_nivel_cone', 'niveles_cones_id', 'cartera_servicios_id');
    }
}
