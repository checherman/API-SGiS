<?php

namespace App\Http\Controllers\V1\Catalogos;

use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Support\Facades\Request;
use \Validator,\Hash, \Response, \DB;

use App\Models\Catalogos\Clues;
use App\Models\Sistema\Usuario;
/**
 * Controlador Clues
 *
 * @package    UGUS API
 * @subpackage Controlador
 * @author     Luis Alberto Valdez Lescieur <luisvl13@gmail.com>
 * @created    2017-03-22
 *
 * Controlador `Clues`: Controlador  para el manejo de clues
 *
 */
class CluesController extends Controller
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
                $order = "clues"; $orden = "asc";
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
                $data = Clues::with('jurisdicciones')->with('municipios')->orderBy($order,$orden);

                $search = trim($valor);
                $keyword = $search;
                $data = $data->whereNested(function($query) use ($keyword){
                    $query->where("clues", "LIKE", '%'.$keyword.'%')
                        ->orWhere("nombre", "LIKE", '%'.$keyword.'%');
                });

                $total = $data->get();
                $data = $data->skip($pagina-1)->take($datos['limite'])->get();
            }
            else{
                $data = Clues::with('jurisdicciones')->with('municipios')->skip($pagina-1)->take($datos['limite'])->orderBy($order, $orden)->get();
                $total = Clues::all();
            }

        }
        else{
            $data = Clues::with('jurisdicciones')->with('municipios')->get();
            $total = $data;
        }

        foreach ($data as $key => $value) {
            $usuarios = Usuario::where("cargos_id", "!=", NULL)->where("clues", $value->clues)
                ->with("cargos")
                ->get();

            $value->usuarios = $usuarios;
        }

        if(!$data){
            return Response::json(array("status" => 404,"messages" => "No hay resultados"), 404);
        }
        else{
            return Response::json(array("status" => 200,"messages" => "Operación realizada con exito","data" => $data,"total" => count($total)), 200);

        }
    }

    /**
     * @param Clues $clues
     * @return mixed
     */
    public function show($clues)
    {
        $data = Clues::where('clues', $clues)
                    ->with('jurisdicciones','municipios')
                    ->first();

        $usuarios = Usuario::where("cargos_id", "!=", NULL)
            ->with("cargos")
            ->get();

        $data->usuarios = $usuarios;

        return Response::json([ 'data' => $data], 200);
    }
}