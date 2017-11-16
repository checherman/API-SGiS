<?php
namespace App;

namespace App\Models\Transacciones;
use App\Models\BaseModel;
use App\Models\Sistema\Multimedias;

class Referencias extends BaseModel
{

    public $incrementing = true;

    protected $table = "referencias";
    protected $fillable = ["id", "incidencias_id", "medico_refiere_id", "diagnostico", "resumen_clinico", "clues_origen", "clues_destino", "img", "esContrareferencia"];
    protected $hidden = ["updated_at", "deleted_at"];

    public function incidencias()
    {
        return $this->belongsTo(Incidencias::class);
    }

    public function multimedias()
    {
        return $this->hasMany(Multimedias::class);
    }
}