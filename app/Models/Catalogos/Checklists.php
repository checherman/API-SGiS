<?php

namespace App;

namespace App\Models\Catalogos;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Checklists extends Model
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "checklists";
    protected $fillable = ["id","nombre"];

    public function items(){
        return $this->hasMany(Items::class,'checklists_id','id');
    }

    public function nivelesCones(){
        return $this->belongsToMany(NivelesCones::class, 'checklist_nivel_cone', 'checklists_id', 'niveles_cones_id');
    }
}
