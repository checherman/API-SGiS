<?php

namespace App\Http\Controllers\V1\Transacciones;


use App\Http\Requests;
use App\Http\Controllers\Controller;
use Request;
use \Validator,\Hash, \Response, \DB;
use Carbon\Carbon;

use App\Models\Transacciones\Incidencias;
use App\Models\Transacciones\Responsables;


/**
 * Controlador Incidencia
 *
 * @package    UGUS API
 * @subpackage Controlador
 * @author     Luis Alberto Valdez Lescieur <luisvl13@gmail.com>
 * @created    2017-07-25
 *
 * Controlador `Incidencia`: Controlador  para el manejo de incidencias
 *
 */
class PantallaInformativaController extends Controller
{
    /**
     * @api {get} /incidencias 1.Listar Directorio Apoyo
     * @apiVersion 1.0.0
     * @apiName GetIncidencia
     * @apiGroup Transaccion/IncidenciaController
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
    public function index()
    {
        $estadosIncidencias  = array();
        $cluesH = Request::header('clues');
        $datos = Request::all();
        $edoIncidencia = null;

        if(isset($datos['edo_incidencia'])){
            $edoIncidencia = $datos['edo_incidencia'];
        }

        $now = Carbon::now();
        $fechaActual = $now->toDateTimeString();
        $fechaAnterior = $now->subHours(72)->toDateTimeString();
        //dd($fechaAnterior);
        // Si existe el parametro pagina en la url devolver las filas según sea el caso
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
                $order = "incidencias.created_at"; $orden = "desc";
            }

            if($pagina == 0 || $pagina == null){
                $pagina = 1;
            }


            if($pagina == 1)
                $datos["limite"] = $datos["limite"] - 1;
            // si existe buscar se realiza esta linea para devolver las filas que en el campo que coincidan con el valor que el usuario escribio
            // si no existe buscar devolver las filas con el limite y la pagina correspondiente a la paginación
            $data = Incidencias::select('incidencias.*')
                ->with("pacientes.personas","pacientes.acompaniantes.personas")
                ->with("movimientos_incidencias", "referencias", "altas_incidencias", "estados_incidencias");

            if(!$edoIncidencia == null){
                $data = $data->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
                    ->where('incidencia_clue.clues',$cluesH)
                    ->where('estados_incidencias_id', $edoIncidencia)
                    ->whereBetween('incidencias.created_at', array($fechaAnterior, $fechaActual))
                    ->orderBy($order, $orden);
            }else{
                $data = $data->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
                    ->where('incidencia_clue.clues',$cluesH)
                    ->whereBetween('incidencias.created_at', array($fechaAnterior, $fechaActual))
                    ->orderBy($order, $orden);
            }
            $total = $data->get();
            $data = $data->skip($pagina-1)->take($datos['limite'])->orderBy('incidencias.id', $orden)
                ->get();
        }
        else{
            $data = Incidencias::select('incidencias.*')
                ->with("pacientes.personas", "pacientes.acompaniantes.personas")
                ->with("movimientos_incidencias", "referencias", "altas_incidencias", "estados_incidencias")
                ->whereBetween('incidencias.created_at', array($fechaAnterior, $fechaActual))
                ->get();

            $total = $data;
        }

        foreach ($data as $key => $value) {
            $clues = DB::table('incidencia_clue')->where('incidencias_id', $value->id)->first();
            $value->clues = $clues->clues;
        }


        foreach ($data as $key => $value) {
            foreach ($value->pacientes as $keyPaciente => $valuePaciente){
                $dt = Carbon::parse($valuePaciente->personas->fecha_nacimiento);
                $anioNacimiento = $dt->year;
                $mesNacimiento = $dt->month;
                $diaNacimiento = $dt->day;

                $edad = Carbon::createFromDate($anioNacimiento,$mesNacimiento,$diaNacimiento)->age;
                $valuePaciente->personas->edad = $edad;
            }
        }

        if(!$data){
            return Response::json(array("status" => 404,"messages" => "No hay resultados"), 404);
        }
        else{
            return Response::json(array("status" => 200,"messages" => "Operación realizada con exito","data" => $data,"total" => count($total)), 200);

        }


    }

}
