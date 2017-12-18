<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\BaseModel;
use App\Models\Sistema\SisUsuario;
use Illuminate\Database\Eloquent\SoftDeletes;

class Municipios extends BaseModel
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "municipios";
    protected $fillable = ["id", "clave", "nombre", "jurisdicciones_id"];

    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function jurisdicciones()
    {
        return $this->belongsTo(Jurisdicciones::class);
    }

    public function localidades()
    {
        return $this->hasMany(Localidades::class);
    }

    public function clues()
    {
        return $this->hasMany(Clues::class);
    }

    public function personas()
    {
        return $this->hasMany(Personas::class);
    }

    public function SisUsuario()
    {
        return $this->hasMany(SisUsuario::class);
    }
}
