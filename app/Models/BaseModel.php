<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Session;
abstract class BaseModel extends Model {

    use SoftDeletes;

    public static function boot(){
        parent::boot();

//        static::creating(function($item){
//            if(Session::get('usuario')){
//                $item->creado_por = Session::get('usuario')->id;
//            }
//        });
//
//        static::updating(function($item){
//            if(Session::get('usuario')){
//                $item->modificado_por = Session::get('usuario')->id;
//            }
//        });
//
//        static::deleting(function($item){
//            if(Session::get('usuario')){
//                $item->borrado_por = Session::get('usuario')->id;
//            }
//        });
    }
}
