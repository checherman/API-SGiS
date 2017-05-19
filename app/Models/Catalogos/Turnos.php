<?php

namespace App;

namespace App\Models\Catalogos;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Turnos extends Model
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "turnos";
    protected $fillable = ["id", "nombre", "hora_entrada", "hora_salida"];

    public function clues()
    {
        return $this->belongsToMany(Clues::class, 'clue_turno', 'turno_id', 'clues_id');
    }

    public function diaFestivo()
    {
        return $this->hasOne(DiasFestivos::class);
    }
}
