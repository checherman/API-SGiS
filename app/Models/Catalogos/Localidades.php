<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\BaseModel;
use App\Models\Transacciones\Personas;
use Illuminate\Database\Eloquent\SoftDeletes;

class Localidades extends BaseModel
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "localidades";
    protected $fillable = ["id", "clave", "nombre", "numeroLatitud", "numeroLongitud", "numeroAltitud", "claveCarta", "municipios_id"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function jurisdicciones()
    {
        return $this->belongsTo(Jurisdicciones::class,'jurisdicciones_id','id');
    }

    public function municipios()
    {
        return $this->belongsTo(Municipios::class);
    }

    public function personas()
    {
        return $this->hasMany(Personas::class);
    }
}
