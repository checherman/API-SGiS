<?php

namespace App;

namespace App\Models\Catalogos;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TriageSintomas extends Model
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "triage_sintomas";
    protected $fillable = ["id", "nombre", "descripcion", "triage_id"];

    public function triage()
    {
        return $this->belongsTo(Triage::class,'triage_id','id');
    }
}
