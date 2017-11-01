<?php

namespace App\Http\Controllers\V1\Transacciones;

use App\Http\Controllers\Controller;

use App\Models\Catalogos\ApoyoDirectorioApoyo;
use App\Models\Catalogos\DirectorioApoyos;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\Response as HttpResponse;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use \Validator,\Hash, \Response, \DB;

class DirectorioApoyoController extends Controller
{
    /**
     * Muestra una lista de los recurso según los parametros a procesar en la petición.
     *
     * <h3>Lista de parametros Request:</h3>
     * <Ul>Paginación
     * <Li> <code>$pagina</code> numero del puntero(offset) para la sentencia limit </ li>
     * <Li> <code>$limite</code> numero de filas a mostrar por página</ li>
     * </Ul>
     * <Ul>Busqueda
     * <Li> <code>$valor</code> string con el valor para hacer la busqueda</ li>
     * <Li> <code>$order</code> campo de la base de datos por la que se debe ordenar la información. Por Defaul es ASC, pero si se antepone el signo - es de manera DESC</ li>
     * </Ul>
     *
     * Clues ordenamiento con respecto a clues:
     * <code>
     * http://url?pagina=1&limite=5&order=id ASC
     * </code>
     * <code>
     * http://url?pagina=1&limite=5&order=-id DESC
     * </code>
     *
     * Todo Los parametros son opcionales, pero si existe pagina debe de existir tambien limite
     * @return Response
     * <code style="color:green"> Respuesta Ok json(array("status": 200, "messages": "Operación realizada con exito", "data": array(resultado)),status) </code>
     * <code> Respuesta Error json(array("status": 404, "messages": "No hay resultados"),status) </code>
     */
    public function index(){
        $datos = Request::all();

        // Si existe el paarametro pagina en la url devolver las filas según sea el caso
        // si no existe parametros en la url devolver todos las filas de la tabla correspondiente
        // esta opción es para devolver todos los datos cuando la tabla es de tipo catálogo
        if(array_key_exists('pagina', $datos)){
            $pagina = $datos['pagina'];
            if(isset($datos['order'])){
                $order = $datos['order'];
                if(strpos(" ".$order,"-"))
                    $orden = "desc";
                else
                    $orden = "asc";
                $order = str_replace("-", "", $order);
            }
            else{
                $order = "id"; $orden = "asc";
            }

            if($pagina == 0){
                $pagina = 1;
            }
            if($pagina == 1)
                $datos["limite"] = $datos["limite"] - 1;
            // si existe buscar se realiza esta linea para devolver las filas que en el campo que coincidan con el valor que el usuario escribio
            // si no existe buscar devolver las filas con el limite y la pagina correspondiente a la paginación
            if(array_key_exists('buscar', $datos)){
                $columna = $datos['columna'];
                $valor   = $datos['valor'];
                $data = DirectorioApoyos::orderBy($order,$orden);

                $search = trim($valor);
                $keyword = $search;
                $data = $data->whereNested(function($query) use ($keyword){
                    $query->where("id", "LIKE", '%'.$keyword.'%')
                        ->orWhere("institucion", "LIKE", '%'.$keyword.'%')
                        ->orWhere("direccion", "LIKE", '%'.$keyword.'%')
                        ->orWhere("responsable", "LIKE", '%'.$keyword.'%')
                        ->orWhere("telefono", "LIKE", '%'.$keyword.'%');
                });

                $total = $data->get();
                $data = $data->skip($pagina-1)->take($datos['limite'])->get();
            }
            else{
                $data = DirectorioApoyos::skip($pagina-1)->take($datos['limite'])->orderBy($order, $orden)->get();
                $total = DirectorioApoyos::all();
            }

        }
        else{
            $data = DirectorioApoyos::get();
            $total = $data;
        }

        if(!$data){
            return Response::json(array("status" => 404,"messages" => "No hay resultados"), 404);
        }
        else{
            return Response::json(array("status" => 200,"messages" => "Operación realizada con exito","data" => $data,"total" => count($total)), 200);

        }
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

        $errors_main = array();
        DB::beginTransaction();

        try {
            $validacion = $this->ValidarParametros("", NULL, $datos);
            if ($validacion != "") {
                return Response::json(['error' => $validacion], HttpResponse::HTTP_CONFLICT);
            }

            $data = new DirectorioApoyos;
            $data->institucion = $datos['institucion'];
            $data->responsable = $datos['responsable'];
            $data->direccion = $datos['direccion'];
            $data->correo = $datos['correo'];
            $data->telefono = $datos['telefono'];
            $data->municipios_id = $datos['municipios_id'];
            $data->localidades_id = $datos['localidades_id'];

            if ($data->save()) {
                $datos = (object)$datos;
                $success = $this->AgregarDatos($datos, $data);
            }

        } catch (\Exception $e){
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
        $data = DirectorioApoyos::find($id);

        if(!$data ){

            return Response::json(['error' => "No se encuentra el recurso que esta buscando."], HttpResponse::HTTP_NOT_FOUND);
        }

        $apoyos = ApoyoDirectorioApoyo::select("id","nombre","descripcion")->where("directorio_apoyos_id", $id)
            ->join('apoyos', 'apoyos.id', '=', 'apoyo_directorio_apoyo.apoyos_id')
            ->get();

        $data->apoyos = $apoyos;

        return Response::json([ 'data' => $data ], HttpResponse::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
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

        $success = false;

        DB::beginTransaction();
        try{
            $data = DirectorioApoyos::find($id);
            $data->institucion = $datos->institucion;
            $data->responsable = $datos->responsable;
            $data->direccion = $datos->direccion;
            $data->correo = $datos->correo;
            $data->telefono = $datos->telefono;
            $data->municipios_id = $datos->municipios_id;
            $data->localidades_id = $datos->localidades_id;


            if ($data->save()) {
                $datos = (object)$datos;
                $success = $this->AgregarDatos($datos, $data);
            }
        }
        catch (\Exception $e){
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
			$object = DirectorioApoyos::destroy($id);
			return Response::json(['data'=>$object],200);
		} catch (Exception $e) {
		   return Response::json(['error' => $e->getMessage()], HttpResponse::HTTP_CONFLICT);
		}
    }

    /**
     * Validad los parametros recibidos, Esto no tiene ruta de acceso es un metodo privado del controlador.
     *
     * @param  Request  $request que corresponde a los parametros enviados por el cliente
     *
     * @return Response
     * <code> Respuesta Error json con los errores encontrados </code>
     */
    private function ValidarParametros($key, $id, $request){

        $messages = [
            'required' => 'required',
            'email'         => "email",
            'unique' => 'unique'
        ];

        $rules = [
            'responsable' => 'required',
            'direccion' => 'required',
            'correo'        => 'required|email',
        ];
        //dd($rules);
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

        $success = false;
        //verificar si existe items, en caso de que exista proceder a guardarlo
        if(property_exists($datos, "apoyos")){
            //limpiar el arreglo de posibles nullos
            $detalle = array_filter($datos->apoyos, function($v){return $v !== null;});

            //borrar los datos previos de articulo para no duplicar información
            ApoyoDirectorioApoyo::where("directorio_apoyos_id", $data->id)->delete();

            //recorrer cada elemento del arreglo
            foreach ($detalle as $key => $value) {
                //validar que el valor no sea null
                if($value != null){
                    //comprobar si el value es un array, si es convertirlo a object mas facil para manejar.
                    if(is_array($value))
                        $value = (object) $value;

                    //comprobar que el dato que se envio no exista o este borrado, si existe y esta borrado poner en activo nuevamente
                    DB::update("update apoyo_directorio_apoyo set deleted_at = null where directorio_apoyos_id = $data->id and apoyos_id = $value->id");

                    //si existe actualizar
                    $apoyos = ApoyoDirectorioApoyo::where("directorio_apoyos_id", $data->id)->where("apoyos_id", $value->id)->first();

                    //si no existe crear
                    if(!$apoyos)
                        $apoyos = new ApoyoDirectorioApoyo;

                    $apoyos->directorio_apoyos_id 	= $data->id;
                    $apoyos->apoyos_id              = $value->id;

                    if($apoyos->save()){
                        $success = true;
                    }
                }
            }
        }

        return $success;

    }
}
