<?php
namespace App\Models\Sistema;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notificaciones extends BaseModel{

    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = 'notifications';
    protected $fillable = ["id", "type", "notifiable", "data", "read_at"];
}