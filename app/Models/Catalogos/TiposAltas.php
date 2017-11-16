<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\BaseModel;

class TiposAltas extends BaseModel
{
    public $incrementing = true;

    protected $table = "tipos_altas";
    protected $fillable = ["id", "nombre", "descripcion"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];
}
