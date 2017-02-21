<?php
namespace App\Models\Sistema;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class SisLogs extends BaseModel{

    use SoftDeletes;
    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    protected $table = 'sis_logs';
    protected $fillable = ["id"];

}