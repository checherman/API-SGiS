<?php
namespace App\Models\Sistema;

use App\Models\BaseModel;
use App\Models\Transacciones\AltasIncidencias;
use App\Models\Transacciones\Referencias;
use Illuminate\Database\Eloquent\SoftDeletes;

class Multimedias extends BaseModel{

    use SoftDeletes;

    protected $generarID = true;
    protected $guardarIDServidor = true;
    protected $guardarIDUsuario = false;
    public $incrementing = false;

    protected $table = 'multimedias';
    protected $fillable = ["id", "servidor_id", "incremento", "referencias_id", "altas_incidencias_id", "tipo", "url"];

    public function altas_incidencias()
    {
        return $this->belongsTo(AltasIncidencias::class);
    }

    public function referencias()
    {
        return $this->belongsTo(Referencias::class);
    }

}