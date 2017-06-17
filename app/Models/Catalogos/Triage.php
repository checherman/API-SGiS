<?php

namespace App;

namespace App\Models\Catalogos;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Triage extends Model
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "triage";
    protected $fillable = ["id", "nombre", "descripcion"];

    public function triageSintomas(){
        return $this->hasMany(TriageSintomas::class,'triage_id','id');
    }
}
