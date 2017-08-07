<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\BaseModel;
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
}
