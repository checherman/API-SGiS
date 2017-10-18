<?php
namespace App\Models\Sistema;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SisLogs extends Model{
    use SoftDeletes;
    protected $table = 'sis_logs';  
    protected $fillable = ['usuarios_id', 'ip', 'mac', 'tipo', 'ruta', 'controlador', 'tabla', 'peticion', 'respuesta', 'info'];    
}