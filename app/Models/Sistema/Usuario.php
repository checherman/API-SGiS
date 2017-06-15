<?php

namespace App\Models\Sistema;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Authenticatable;

class Usuario extends BaseModel implements Authenticatable{

    use SoftDeletes;
    protected $generarID = false;
    protected $guardarIDUsuario = false;
    protected $fillable = ["id", "servidor_id", "password", "nombre", "paterno", "materno", "celular", "avatar", "su", "cargos_id"];

    public function scopeObtenerClavesPermisos($query){
        return $query->select('permisos.id AS clavePermiso')
            ->leftjoin('rol_usuario','usuario_id','=','usuarios.id')
            ->leftjoin('permiso_rol','permiso_rol.rol_id','=','rol_usuario.rol_id')
            ->leftjoin('permisos','permisos.id','=','permiso_rol.permiso_id')
            ->groupBy('clavePermiso','usuarios.id');
    }

    public function roles(){
        return $this->belongsToMany('App\Models\Sistema\Rol', 'rol_usuario', 'usuario_id', 'rol_id');
    }


    /**
     * @return string
     */
    public function getAuthIdentifierName()
    {
        // Return the name of unique identifier for the user (e.g. "id")
    }

    /**
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        // Return the unique identifier for the user (e.g. their ID, 123)
    }

    /**
     * @return string
     */
    public function getAuthPassword()
    {
        // Returns the (hashed) password for the user
    }

    /**
     * @return string
     */
    public function getRememberToken()
    {
        // Return the token used for the "remember me" functionality
    }

    /**
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        // Store a new token user for the "remember me" functionality
    }

    /**
     * @return string
     */
    public function getRememberTokenName()
    {
        // Return the name of the column / attribute used to store the "remember me" token
    }

}
