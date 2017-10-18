<?php

namespace App\Http\Controllers\V1\Sistema;

use App\Http\Controllers\Controller;

use App\Models\Catalogos\RolUsuario;
use Request;
use Illuminate\Http\Response as HttpResponse;

use App\Http\Requests;
use App\Models\Sistema\Usuario;
use Illuminate\Support\Facades\Input;
use \Validator,\Hash, \Response, \DB;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parametros = Input::only('q','page','per_page');
        if ($parametros['q']) {
             $usuarios =  Usuario::where('su',false)->whereNotNull('password')->where(function($query) use ($parametros) {
                 $query->where('id','LIKE',"%".$parametros['q']."%")->orWhere(DB::raw("CONCAT(nombre,' ',paterno,' ',materno)"),'LIKE',"%".$parametros['q']."%");
             });
        } else {
             $usuarios =  Usuario::where('su',false)->whereNotNull('password')->with("clues");
        }

        if(isset($parametros['page'])){
            $resultadosPorPagina = isset($parametros["per_page"])? $parametros["per_page"] : 20;
            $usuarios = $usuarios->paginate($resultadosPorPagina);
        } else {
            $usuarios = $usuarios->get();
        }

        return Response::json([ 'data' => $usuarios],200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $datos = Input::json()->all();
        //dd($datos);
        $errors_main = array();
        DB::beginTransaction();

        try {
            $validacion = $this->ValidarParametros("", NULL, $datos);
            if ($validacion != "") {
                return Response::json(['error' => $validacion], HttpResponse::HTTP_CONFLICT);
            }

            $data = new Usuario;
            $data->id = $datos['id'];
            $data->servidor_id = env("SERVIDOR_ID");
            $data->password = $datos['password'];
            $data->nombre = $datos['nombre'];
            $data->paterno = $datos['paterno'];
            $data->materno = $datos['materno'];
            $data->celular = $datos['celular'];
            $data->avatar = $datos['avatar'];
            $data->clues = $datos['clues'];
            $data->password = Hash::make($datos['password']);

            $data->save();
            $datos = (object)$datos;
            $this->AgregarDatos($datos, $data);
            $success = true;

        } catch (Exception $e){
            return Response::json($e->getMessage(), 500);
        }

        if ($success){
            DB::commit();
            return Response::json(array("status" => 201,"messages" => "Creado","data" => $data), 201);
        } else{
            DB::rollback();
            return Response::json(array("status" => 409,"messages" => "Conflicto"), 409);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Usuario::find($id);

        if(!$data ){

            return Response::json(['error' => "No se encuentra el recurso que esta buscando."], HttpResponse::HTTP_NOT_FOUND);
        }
        unset($data->password);

        $roles = RolUsuario::select("rol_usuario.rol_id as id","roles.nombre")->where("usuario_id", $id)
            ->join('roles', 'roles.id', '=', 'rol_usuario.rol_id')
            ->get();

        $data->roles = $roles;

        return Response::json([ 'data' => $data ], HttpResponse::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int $id
     * @return HttpResponse
     */
    public function update(Request $request, $id)
    {
        $datos = Request::json()->all();

        $validacion = $this->ValidarParametros("", $id, $datos);
        if($validacion != ""){
            return Response::json(['error' => $validacion], HttpResponse::HTTP_CONFLICT);
        }

        if(is_array($datos))
            $datos = (object) $datos;

        DB::beginTransaction();
        try{
            $data = Usuario::find($id);
            $data->id = $datos->id;
            $data->servidor_id = env("SERVIDOR_ID");
            $data->password = $datos->password;
            $data->nombre = $datos->nombre;
            $data->paterno = $datos->paterno;
            $data->materno = $datos->materno;
            $data->celular = $datos->celular;
            $data->avatar = $datos->avatar;
            $data->clues = $datos->clues;

            if ( $datos->password ){
                $data->password = Hash::make($datos->password);
            }

            $data->save();
            $this->AgregarDatos($datos, $data);
            $success = true;
        }
        catch(\Exception $e){
            return Response::json($e->getMessage(), 500);
        }

        if ($success){
            DB::commit();
            return Response::json(array("status" => 200, "messages" => "Operación realizada con exito", "data" => $data), 200);
        }
        else {
            DB::rollback();
            return Response::json(array("status" => 304, "messages" => "No modificado"),304);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $object = Usuario::destroy($id);
            return Response::json(['data'=>$object],200);
        } catch (Exception $e) {
            return Response::json(['error' => $e->getMessage()], HttpResponse::HTTP_CONFLICT);
        }
    }

    /**
     * Validad los parametros recibidos, Esto no tiene ruta de acceso es un metodo privado del controlador.
     *
     * @param $key
     * @param $id
     * @param  Request $request que corresponde a los parametros enviados por el cliente
     * @return Response <code> Respuesta Error json con los errores encontrados </code>
     * <code> Respuesta Error json con los errores encontrados </code>
     */
    private function ValidarParametros($key, $id, $request)
    {
        $messages = [

            'required'      => "required",
            'email'         => "email",
            'unique'        => "unique"
        ];

        $rules = [
            'id'            => 'required|email|unique:usuarios,id,'.$id.',id,deleted_at,NULL',
            'password'      => 'required',
            'nombre'        => 'required',
            'paterno'       => 'required',
            'materno'       => 'required',
            'celular'       => 'required'
        ];

        $v = Validator::make($request, $rules, $messages);

        if ($v->fails()){
            $mensages_validacion = array();
            foreach ($v->errors()->messages() as $indice => $item) { // todos los mensajes de todos los campos
                $msg_validacion = array();
                foreach ($item as $msg) {
                    array_push($msg_validacion, $msg);
                }
                array_push($mensages_validacion, array($indice.''.$key => $msg_validacion));
            }
            return $mensages_validacion;
        }else{
            return ;
        }
    }

    private function AgregarDatos($datos, $data){
        //verificar si existe items, en caso de que exista proceder a guardarlo
        if(property_exists($datos, "roles")){
            //limpiar el arreglo de posibles nullos
            $detalle = array_filter($datos->roles, function($v){return $v !== null;});

            //recorrer cada elemento del arreglo
            foreach ($detalle as $key => $value) {
                //validar que el valor no sea null
                if($value != null){
                    //comprobar si el value es un array, si es convertirlo a object mas facil para manejar.
                    if(is_array($value))
                        $value = (object) $value;

                    //borrar los datos previos de articulo para no duplicar información
                    RolUsuario::where("rol_id", $value->id)->where("usuario_id", $data->id)->delete();

                    //si existe actualizar
                    $roles = RolUsuario::where("usuario_id", $data->id)->where("rol_id", $value->id)->first();

                    //si no existe crear
                    if(!$roles)
                        $roles = new RolUsuario;

                    $roles->rol_id 	    = $value->id;
                    $roles->usuario_id  = $data->id;

                    $roles->save();
                }
            }
        }

    }
}
