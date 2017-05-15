<?php

namespace App;

namespace App\Models\Catalogos;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Items extends Model
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "items";
    protected $fillable = ["id","nombre", "checklists_id"];

    public function checklist()
    {
        return $this->belongsTo(GruposCie10::class,'checklists_id','id');
    }
}
