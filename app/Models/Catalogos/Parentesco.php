<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\BaseModel;
use App\Models\Transacciones\Acompaniantes;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parentesco extends BaseModel
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "parentescos";
    protected $fillable = ["id","nombre"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function acompaniantes()
    {
        return $this->hasMany(Acompaniantes::class);
    }

}
