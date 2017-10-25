<?php

namespace App\Http\Controllers\V1\Catalogos;

use App\Http\Requests;
use App\Models\Catalogos\EstadosPacientes;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Input;
use \Validator,\Hash, \Response;

use App\Http\Controllers\ApiController;
use App\Models\Catalogos\ValoraciionesPacientes;
/**
 * Controlador EstadoPaciente
 *
 * @package    UGUS API
 * @subpackage Controlador
 * @author     Luis Alberto Valdez Lescieur <luisvl13@gmail.com>
 * @created    2017-03-22
 *
 * Controlador `EstadoPaciente`: Controlador  para el manejo de catalogo valoraciones de pacientes
 *
 */
class EstadoPacienteController extends ApiController
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
     * <Li> <code>$order</code> campo de la base de datos por la que se debe ordenar la información. Por Default es ASC, pero si se antepone el signo - es de manera DESC</ li>
     * </Ul>
     *
     * ValoraciionesPacientes ordenamiento con respecto a id:
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
                $data = EstadosPacientes::orderBy($order,$orden);

                $search = trim($valor);
                $keyword = $search;
                $data = $data->whereNested(function($query) use ($keyword){
                    $query->where('id','LIKE',"%".$keyword['q']."%")
                        ->orWhere('nombre','LIKE',"%".$keyword['q']."%")
                        ->orWhere('descripcion','LIKE',"%".$keyword['q']."%");
                });

                $total = $data->get();
                $data = $data->skip($pagina-1)->take($datos['limite'])->get();
            }
            else{
                $data = EstadosPacientes::skip($pagina-1)->take($datos['limite'])->orderBy($order, $orden)->get();
                $total = EstadosPacientes::all();
            }

        }
        else{
            $data = EstadosPacientes::get();
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
        $mensajes = [

            'required'      => "required",
            'unique'        => "unique"
        ];

        $reglas = [
            'nombre'        => 'required|unique:valoraciones_pacientes',
            'descripcion'   => 'required'
        ];

        $inputs = Input::only('nombre', 'descripcion');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return $this->respuestaError($v->errors(), 409);
        }

        try {

            $data = EstadosPacientes::create($inputs);

            return $this->respuestaVerUno($data,201);

        } catch (\Exception $e) {
            return $this->respuestaError($e->getMessage(), 409);
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
        $data = EstadosPacientes::find($id);

        if(!$data){
            return Response::json(['error' => "No se encuentra el recurso que esta buscando."], HttpResponse::HTTP_NOT_FOUND);
        }

        return $this->respuestaVerUno($data);
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
        $mensajes = [

            'required'      => "required",
            'unique'        => "unique"
        ];

        $reglas = [
            'nombre'        => 'required|unique:valoraciones_pacientes,nombre,'.$id.',id,deleted_at,NULL',
            'descripcion'   => 'required',
        ];

        $inputs = Input::only('nombre', 'descripcion');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return $this->respuestaError($v->errors(), 409);
        }

        try {
            $data = EstadosPacientes::find($id);
            $data->nombre =  $inputs['nombre'];
            $data->descripcion =  $inputs['descripcion'];

            $data->save();
            return $this->respuestaVerUno($data);

        } catch (\Exception $e) {
            return $this->respuestaError($e->getMessage(), 409);
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
            $data = EstadosPacientes::destroy($id);
            return $this->respuestaVerTodo($data);
        } catch (Exception $e) {
            return $this->respuestaError($e->getMessage(), 409);
        }
    }
}

