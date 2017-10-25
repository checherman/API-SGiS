<?php
namespace App\Http\Controllers\V1\Catalogos;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Request;
use Illuminate\Http\Response as HttpResponse;

use App\Http\Requests;
use \Validator,\Hash, \Response, \DB;
use Illuminate\Support\Facades\Input;


use App\Models\Catalogos\GruposCie10;
use App\Models\Catalogos\CategoriasCie10;
use App\Models\Catalogos\SubCategoriasCie10;

/**
 * Controlador GrupoCie10
 *
 * @package    UGUS API
 * @subpackage Controlador
 * @author     Luis Alberto Valdez Lescieur <luisvl13@gmail.com>
 * @created    2017-03-22
 *
 * Controlador `GrupoCie10`: Controlador  para el manejo de grupos del cie10
 *
 */
class GrupoCie10Controller extends Controller
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
     * GruposCie10 ordenamiento con respecto a id:
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
                $data = GruposCie10::orderBy($order,$orden);

                $search = trim($valor);
                $keyword = $search;
                $data = $data->whereNested(function($query) use ($keyword){
                    $query->where('id','LIKE',"%".$keyword['q']."%")
                        ->orWhere('codigo','LIKE',"%".$keyword['q']."%")
                        ->orWhere('nombre','LIKE',"%".$keyword['q']."%");
                });

                $total = $data->get();
                $data = $data->skip($pagina-1)->take($datos['limite'])->get();
            }
            else{
                $data = GruposCie10::skip($pagina-1)->take($datos['limite'])->orderBy($order, $orden)->get();
                $total = GruposCie10::all();
            }

        }
        else{
            $data = GruposCie10::get();
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

        $success = false;
        $errors_main = array();
        DB::beginTransaction();

        try {
            $validacion = $this->ValidarParametros("", NULL, $datos);
            if($validacion != ""){
                return Response::json(['error' => $validacion], HttpResponse::HTTP_CONFLICT);
            }

            $data = new GruposCie10;

            $data->nombre = $datos['nombre'];
            $data->codigo = $datos['codigo'];

            if ($data->save())
                $datos = (object) $datos;
            $this->AgregarDatos($datos, $data);
            $success = true;

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
     * Devuelve la información del registro especificado.
     *
     * @param  int  $id que corresponde al identificador del recurso a mostrar
     *
     * @return Response
     * <code style="color:green"> Respuesta Ok json(array("status": 200, "messages": "Operación realizada con exito", "data": array(resultado)),status) </code>
     * <code> Respuesta Error json(array("status": 404, "messages": "No hay resultados"),status) </code>
     */
    public function show($id){
        $data = GruposCie10::with('CategoriasCie10')->find($id);

        if(!$data){
            return Response::json(array("status" => 204,"messages" => "No hay resultados"), 204);
        }
        else{
            return Response::json(array("status" => 200,"messages" => "Operación realizada con exito","data" => $data), 200);
        }
    }

    /**
     * Actualizar el  registro especificado en el la base de datos
     *
     * <h4>Request</h4>
     * Recibe un Input Request con el json de los datos
     *
     * @param  int  $id que corresponde al identificador del dato a actualizar
     * @return Response
     * <code style="color:green"> Respuesta Ok json(array("status": 200, "messages": "Operación realizada con exito", "data": array(resultado)),status) </code>
     * <code> Respuesta Error json(array("status": 304, "messages": "No modificado"),status) </code>
     */
    public function update($id){
        $datos = Input::json()->all();

        $validacion = $this->ValidarParametros("", $id, $datos);
        if($validacion != ""){
            return Response::json(['error' => $validacion], HttpResponse::HTTP_CONFLICT);
        }

        $success = false;
        DB::beginTransaction();
        try{
            $data = GruposCie10::find($id);

            $data->nombre = $datos['nombre'];
            $data->codigo = $datos['codigo'];

            if ($data->save())
                $datos = (object) $datos;
            $this->AgregarDatos($datos, $data);
            $success = true;

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
     * Elimine el registro especificado del la base de datos (softdelete).
     *
     * @param  int  $id que corresponde al identificador del dato a eliminar
     *
     * @return Response
     * <code style="color:green"> Respuesta Ok json(array("status": 200, "messages": "Operación realizada con exito", "data": array(resultado)),status) </code>
     * <code> Respuesta Error json(array("status": 500, "messages": "Error interno del servidor"),status) </code>
     */
    public function destroy($id)
    {
        $success = false;
        DB::beginTransaction();
        try {
            $data = GruposCie10::find($id);
            if($data)
                $data->delete();
            $success = true;
        }
        catch (\Exception $e){
            return Response::json($e->getMessage(), 500);
        }
        if ($success){
            DB::commit();
            return Response::json(array("status" => 200, "messages" => "Operación realizada con exito","data" => $data), 200);
        }
        else {
            DB::rollback();
            return Response::json(array("status" => 404, "messages" => "No se encontro el registro"), 404);
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
            'unique' => 'unique'
        ];

        /*
        if($request['nivel_cone']) {
            $nivel_cone = $request['nivel_cone'];
        } else {
            $nivel_cone = NULL;
        }
        */
        $rules = [
            'nombre' => 'required|min:3|max:250|unique:grupos_cie10,nombre,'.$id.',id,deleted_at,NULL',
            'codigo' => 'required',
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
        //verificar si existe resguardos, en caso de que exista proceder a guardarlo
        if(property_exists($datos, "categorias_cie10")){
            //limpiar el arreglo de posibles nullos
            $detalle = array_filter($datos->categorias_cie10, function($v){return $v !== null;});
            //borrar los datos previos de articulo para no duplicar información
            CategoriasCie10::where("grupos_cie10_id", $data->id)->delete();
            //recorrer cada elemento del arreglo
            foreach ($detalle as $key => $value) {
                //validar que el valor no sea null
                if($value != null){
                    //comprobar si el value es un array, si es convertirlo a object mas facil para manejar.
                    if(is_array($value))
                        $value = (object) $value;

                    //comprobar que el dato que se envio no exista o este borrado, si existe y esta borrado poner en activo nuevamente
                    DB::update("update categorias_cie10 set deleted_at = null where grupos_cie10_id = $data->id and nombre = '$value->nombre' and codigo = '$value->codigo' ");
                    //si existe el elemento actualizar
                    $categoria = CategoriasCie10::where("grupos_cie10_id", $data->id)->where("nombre", $value->nombre)->where("codigo", $value->codigo)->first();
                    //si no existe crear
                    if(!$categoria)
                        $categoria = new CategoriasCie10;

                    $categoria->grupos_cie10_id 	= $data->id;
                    $categoria->nombre              = $value->nombre;
                    $categoria->codigo              = $value->codigo;

                    if ($categoria->save()){
                        if(property_exists($value, "sub_categorias_cie10")){

                            //limpiar el arreglo de posibles nullos
                            $detalle = array_filter($value->sub_categorias_cie10, function($v){return $v !== null;});
                            //borrar los datos previos de articulo para no duplicar información

                            SubCategoriasCie10::where("categorias_cie10_id", $categoria->id)->delete();

                            //recorrer cada elemento del arreglo
                            foreach ($detalle as $key => $val) {
                                //validar que el valor no sea null
                                if($val != null){
                                    //comprobar si el value es un array, si es convertirlo a object mas facil para manejar.
                                    if(is_array($val))
                                        $val = (object) $val;

                                    //comprobar que el dato que se envio no exista o este borrado, si existe y esta borrado poner en activo nuevamente
                                    DB::update("update subcategorias_cie10 set deleted_at = null where categorias_cie10_id = $categoria->id and nombre = '$val->nombre' and codigo = '$val->codigo' ");
                                    //si existe el elemento actualizar
                                    $subCategoria = SubCategoriasCie10::where("categorias_cie10_id", $categoria->id)->where("nombre", $val->nombre)->where("codigo", $val->codigo)->first();
                                    //si no existe crear
                                    if(!$subCategoria)
                                        $subCategoria = new SubCategoriasCie10;

                                    $subCategoria->categorias_cie10_id 	= $categoria->id;
                                    $subCategoria->nombre               = $val->nombre;
                                    $subCategoria->codigo               = $val->codigo;

                                    $subCategoria->save();
                                }
                            }
                        }
                    }

                }
            }


        }
    }
}
