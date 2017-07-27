<?php
namespace App;

namespace App\Models\Transacciones;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Referencias extends BaseModel
{
    use SoftDeletes;

    protected $generarID = true;
    protected $guardarIDServidor = true;
    protected $guardarIDUsuario = false;
    public $incrementing = false;

    protected $table = "referencias";
    protected $fillable = ["id", "servidor_id", "incidencias_id", "medico_refiere_id", "diagnostico"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];
}