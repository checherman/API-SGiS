<?php
namespace App\Models\Sistema;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rol extends BaseModel{

    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = 'roles';
    protected $fillable = ["nombre"];


    public function permisos(){
        return $this->belongsToMany('App\Models\Sistema\Rol', 'permiso_rol', 'rol_id', 'permiso_id');
    }

    public function usuarios(){
        return $this->belongsToMany('App\Models\Sistema\Usuario', 'rol_usuario', 'rol_id', 'usuario_id');
    }
}