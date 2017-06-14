<?php

namespace App;

namespace App\Models\Catalogos;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarteraServicios extends Model
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "cartera_servicios";
    protected $fillable = ["id","nombre"];

    public function items(){
        return $this->hasMany(Items::class,'cartera_servicios_id','id');
    }

    public function nivelesCones(){
        return $this->belongsToMany(NivelesCones::class, 'cartera_servicio_nivel_cone', 'cartera_servicios_id', 'niveles_cones_id');
    }
}
