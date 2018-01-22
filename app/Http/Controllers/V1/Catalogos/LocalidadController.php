<?php

namespace App\Http\Controllers\V1\Catalogos;

use App\Http\Requests;
use Illuminate\Support\Facades\Request;
use \Validator,\Hash, \Response;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\Localidades;

/**
 * Controlador Localidad
 *
 * @package    UGUS API
 * @subpackage Controlador
 * @author     Luis Alberto Valdez Lescieur <luisvl13@gmail.com>
 * @created    2017-03-22
 *
 * Controlador `Localidad`: Controlador  para el manejo de catalogo Localidades
 *
 */
class LocalidadController extends Controller
{
    /**
     * @api {get} /localidades 1.Listar localidades
     * @apiVersion 1.0.0
     * @apiName GetLocalidades
     * @apiGroup Catalogo/LocalidadController
     *
     * @apiDescription Muestra una lista de los recurso según los parametros a procesar en la petición
     *
     * @apiPermission Admin
     *
     * @apiParam {Number} pagina Numero del puntero(offset) para la sentencia limit.
     * @apiParam {Number} limite Numero de filas a mostrar por página.
     * @apiParam {Boolean} buscar Mandar por defecto true, para realizar la busqueda.
     *
     * @apiParam {String} valor Valor para hacer la busqueda.
     * @apiParam {String} order Campo de la base de datos por la que se debe ordenar la información. Por Default es ASC, pero si se antepone el signo - es de manera DESC.
     *
     * @apiParamExample {json} Ordenamiento - Ejemplo:
    http://url?pagina=1&limite=5&order=id ASC
    http://url?pagina=1&limite=5&order=id DESC
    Todo Los parametros son opcionales, pero si existe pagina debe de existir tambien limite
     *
     * @apiParamExample {json} Busqueda - Ejemplo:
    http://url?valor=busqueda&buscar=true
     *
     * @apiSuccess {Object[]} data Lista.
     * @apiSuccess {String} messages Mensaje de Operación realizada con exito.
     * @apiSuccess {Number} status Estatus 200.
     * @apiSuccess {Number} total Total de datos devueltos.
     *
     * @apiSuccessExample Respuesta exitosa:
     *     HTTP/1.1 200 OK
     *     {
     *       "data": [{},{}...],
     *       "messages": "Operación realizada con exito",
     *       "status": 200,
     *       "total": TotalDeDatosDevueltos
     *     }
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
                $data = Localidades::with("municipios")
                    ->selectRaw("municipios.id as idMunicipio, municipios.nombre as municipios, localidades.id, localidades.claveCarta, localidades.nombre, localidades.created_at, localidades.updated_at, localidades.deleted_at")
                    ->leftJoin('municipios', 'municipios.id', '=', 'localidades.municipios_id')
                    ->orderBy($order,$orden);

                $search = trim($valor);
                $keyword = $search;
                $data = $data->whereNested(function($query) use ($keyword){
                    $query->Where('localidades.nombre', 'LIKE', '%'.$keyword.'%')
                        ->orWhere('municipios.nombre', 'LIKE', '%'.$keyword.'%')
                        ->orWhere('localidades.id', $keyword)
                        ->orWhere('municipios.id', $keyword);
                });

                $total = $data->get();
                $data = $data->skip($pagina-1)->take($datos['limite'])->get();
            }
            else{
                $data = Localidades::with("municipios")
                    ->selectRaw("municipios.id as idMunicipio, municipios.nombre as municipios, localidades.id, localidades.claveCarta, localidades.nombre, localidades.created_at, localidades.updated_at, localidades.deleted_at")
                    ->leftJoin('municipios', 'municipios.id', '=', 'localidades.municipios_id')
                    ->skip($pagina-1)->take($datos['limite'])->orderBy($order, $orden)->get();

                $total = Localidades::all();
            }

        }
        else{
            $data = Localidades::with("municipios")
                ->selectRaw("municipios.id as idMunicipio, municipios.nombre as municipios, localidades.id, localidades.claveCarta, localidades.nombre, localidades.created_at, localidades.updated_at, localidades.deleted_at")
                ->leftJoin('municipios', 'municipios.id', '=', 'localidades.municipios_id');

            if(isset($datos["id"])){
                $data = $data->where("municipios.nombre", $datos["id"])->orWhere("municipios.id", $datos["id"]);
            }else{
                $data = $data->where("municipios.id", 1);
            }

            $data = $data->get();
            $total = $data;
        }

        if(!$data){
            return Response::json(array("status" => 404,"messages" => "No hay resultados"), 404);
        }
        else{
            return Response::json(array("status" => 200,"messages" => "Operación realizada con exito","data" => $data,"total" => count($total)), 200);

        }
    }

}
