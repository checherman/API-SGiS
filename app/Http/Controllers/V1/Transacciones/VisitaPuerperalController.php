<?php

namespace App\Http\Controllers\V1\Transacciones;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Transacciones\VisitasPuerperales;
use DateTime;
use Illuminate\Http\Response as HttpResponse;

use Request;

use \Validator,\Hash, \Response, \DB;
use Illuminate\Support\Facades\Input;

use Carbon\Carbon;

use App\Models\Transacciones\AltasIncidencias;

use App\Models\Transacciones\Incidencias;
use App\Models\Transacciones\Pacientes;
use App\Models\Transacciones\Responsables;
use App\Models\Transacciones\Personas;
use App\Models\Transacciones\Acompaniantes;

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
class VisitaPuerperalController extends Controller
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
            if(array_key_exists('buscar', $datos)){
                $columna = $datos['columna'];
                $valor   = $datos['valor'];

                $data = Incidencias::select('incidencias.*')
                    ->join('incidencia_paciente', 'incidencia_paciente.incidencias_id', '=', 'incidencias.id')
                    ->join('pacientes', 'incidencia_paciente.pacientes_id', '=', 'pacientes.id')
                    ->join('personas', 'pacientes.personas_id', '=', 'personas.id')
                    ->with("pacientes.personas","pacientes.acompaniantes.personas")
                    ->with("altas_incidencias", "estados_incidencias");

                if(!$edoIncidencia == null){
                    $data = $data->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
                                ->where('incidencia_clue.clues',$cluesH)
                                ->where('estados_incidencias_id', $edoIncidencia)
                                ->orderBy($order, $orden);
                }else{
                    $data = $data->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
                            ->where('incidencia_clue.clues',$cluesH)
                            ->orderBy($order, $orden);
                }

                $search = trim($valor);
                $keyword = $search;

                $data = $data->whereNested(function($query) use ($keyword){
                    $query->where("incidencias.id", "LIKE", '%'.$keyword.'%')
                            ->orWhere("personas.nombre", "LIKE", '%'.$keyword.'%')
                            ->orWhere("personas.paterno", "LIKE", '%'.$keyword.'%')
                            ->orWhere("personas.materno", "LIKE", '%'.$keyword.'%');
                });

                $total = $data->get();
                $data = $data->skip($pagina-1)->take($datos['limite'])->get();
            }else{

                $data = Incidencias::select('incidencias.*')
                    ->with("pacientes.personas","pacientes.acompaniantes.personas")
                    ->with("altas_incidencias", "estados_incidencias");

                $data = $data->join('altas_incidencias', 'altas_incidencias.incidencias_id', '=', 'incidencias.id')
                    ->whereBetween('altas_incidencias.created_at', array(Carbon::now()->subDays(10)->toDateString(), Carbon::now()->toDateString()));

                if(!$edoIncidencia == null){
                    $data = $data->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
                        ->where('incidencia_clue.clues',$cluesH)
                        ->where('estados_incidencias_id', $edoIncidencia)
                        ->orderBy($order, $orden);
                }else{
                    $data = $data->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
                        ->where('incidencia_clue.clues',$cluesH)
                        ->orderBy($order, $orden);
                }
                $total = $data->get();
                $data = $data->skip($pagina-1)->take($datos['limite'])->orderBy('incidencias.id', $orden)
                    ->get();
            }

        }
        else{
            $data = Incidencias::with("pacientes.personas", "pacientes.acompaniantes.personas")
                ->with("altas_incidencias", "estados_incidencias")
                ->get();

            $total = $data;
        }

        foreach ($data as $key => $value) {
            $clues = DB::table('incidencia_clue')->where('incidencias_id', $value->id)->first();
            $value->clues = $clues->clues;
        }

        foreach ($data as $key => $value) {
            $ahora = new DateTime("Now");
            $created_at = $value->created_at;
            $diff = $created_at->diff($ahora);
            $antiguedad = $this->obtenerAntiguedad($diff);

            $value->antiguedad = $antiguedad;
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

    /**
     * @api {post} /incidencias 2.Crea nueva Incidencia
     * @apiVersion 1.0.0
     * @apiName PostIncidencia
     * @apiGroup Transaccion/IncidenciaController
     * @apiPermission Admin
     *
     * @apiDescription Crea una nueva Incidencia.
     *
     * @apiParam {json} datos json con datos agregar.
     * @apiParamExample {json} Request-Ejemplo:
     *     {
     *        "id": "1812201716028804",
     *        "motivo_ingreso": "sdfsfsdf",
     *        "impresion_diagnostica": "sdfsdfsdfsdfsdfsdf",
     *        "clues": "CSSSA019954",
     *        "estados_incidencias_id": 1,
     *        "tieneReferencia": "",
     *        "pacientes": [
     *             {
     *                "id": "",
     *                "personas_id": "jsiaojdiknaskldna88980",
     *                "personas_id_viejo": "",
     *                "personas": {
     *                   "id": "jsiaojdiknaskldna88980",
     *                   "nombre": "pruebaaaa",
     *                   "paterno": "pruebaaaa",
     *                   "materno": "pruebaaaa",
     *                   "domicilio": "adadsadsadsa",
     *                   "fecha_nacimiento": "1990-02-15",
     *                   "telefono": "965485232",
     *                   "estados_embarazos_id": "2",
     *                   "derechohabientes_id": "3",
     *                   "municipios_id": "3",
     *                   "localidades_id": "420"
     *                },
     *                "acompaniantes": {
     *                   "id": "",
     *                   "personas_id": "asdsadasd78",
     *                   "parentescos_id": "10",
     *                   "esResponsable": 1,
     *                   "personas": {
     *                      "id": "asdsadasd78",
     *                      "nombre": "Luis",
     *                      "paterno": "Valdez",
     *                      "materno": "Lescieur",
     *                      "domicilio": "Conocido",
     *                      "telefono": "965485232"
     *                },
     *             }
     *          ],
     *          "movimientos_incidencias": [
     *             {
     *                "id": "",
     *                "turnos_id": "3",
     *                "ubicaciones_pacientes_id": "6",
     *                "estados_pacientes_id": "1",
     *                "triage_colores_id": "2",
     *                "subcategorias_cie10_id": 7354,
     *                "medico_reporta_id": null,
     *                "indicaciones": null,
     *                "reporte_medico": null,
     *                "diagnostico_egreso": null,
     *                "observacion_trabajo_social": null,
     *                "metodos_planificacion_id": null
     *             }
     *         ],
     *          "referencias": [
     *             {
     *                "id": "",
     *                "medico_refiere_id": "",
     *                "diagnostico": "",
     *                "resumen_clinico": "",
     *                "clues_origen": "",
     *                "clues_destino": "CSSSA019954",
     *                "multimedias": {
     *                   "img": []
     *                },
     *                "esContrareferencia": 0
     *             }
     *         ]
     *     }
     *
     * @apiSuccess {String} id         informacion de la nueva Incidencia.
     *
     */
    public function store()
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

            $data = new Incidencias;

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
     * @api {get} /incidencias/:id 3.Consulta datos de una Incidencia.
     * @apiVersion 1.0.0
     * @apiName ShowIncidencia
     * @apiGroup Transaccion/IncidenciaController
     *
     * @apiDescription Muestra una lista de los recurso según los parametros a procesar en la petición
     *
     * @apiPermission Admin
     *
     * @apiParamExample {json} Ejemplo de uso:
    http://url/1
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
    public function show($id){
        $data = Incidencias::where('id',$id)
            ->with("pacientes.personas", "pacientes.acompaniantes.personas")
            ->with("altas_incidencias")
            ->with("estados_incidencias")
            ->first();

        if(!$data){
            return Response::json(array("status" => 204,"messages" => "No hay resultados"), 204);
        }else{

            $clues = DB::table('incidencia_clue')->where('incidencias_id', $id)->first();
            $data->clues = $clues->clues;

            foreach ($data->movimientos_incidencias as $key => $value) {
                $ahora = new DateTime("Now");
                $created_at = $value->created_at;
                $diff = $created_at->diff($ahora);
                $antiguedad = $this->obtenerAntiguedad($diff);

                $value->antiguedad = $antiguedad;
            }

            foreach ($data->pacientes as $key => $value) {
                $dt = Carbon::parse($value->personas->fecha_nacimiento);
                $anioNacimiento = $dt->year;
                $mesNacimiento = $dt->month;
                $diaNacimiento = $dt->day;

                $edad = Carbon::createFromDate($anioNacimiento,$mesNacimiento,$diaNacimiento)->age;
                $value->personas->edad = $edad;
            }

            if(count($data->referencias) >= 1){
                $data->tieneReferencia = 1;
            }else{
                $data->tieneReferencia = 0;
            }

            return Response::json(array("status" => 200,"messages" => "Operación realizada con exito","data" => $data), 200);
        }
    }

    /**
     * @api {put} /incidencias/:id 4.Actualiza Incidencia
     * @apiVersion 1.0.0
     * @apiName PutIncidencia
     * @apiGroup Transaccion/IncidenciaController
     * @apiPermission Admin
     *
     * @apiDescription Actualiza una Incidencia.
     *
     * @apiParam {number} id de la Incidencia que se quiere editar.
     * @apiParam {json} datos json con datos editar.
     * @apiParamExample {json} Request-Ejemplo:
     *     {
     *        "id": "1812201716028804",
     *        "motivo_ingreso": "sdfsfsdf",
     *        "impresion_diagnostica": "sdfsdfsdfsdfsdfsdf",
     *        "clues": "CSSSA019954",
     *        "estados_incidencias_id": 1,
     *        "tieneReferencia": "",
     *        "pacientes": [
     *             {
     *                "id": 549,
     *                "personas_id": "jsiaojdiknaskldna88980",
     *                "personas_id_viejo": "",
     *                "personas": {
     *                   "id": "jsiaojdiknaskldna88980",
     *                   "nombre": "pruebaaaa",
     *                   "paterno": "pruebaaaa",
     *                   "materno": "pruebaaaa",
     *                   "domicilio": "adadsadsadsa",
     *                   "fecha_nacimiento": "1990-02-15",
     *                   "telefono": "965485232",
     *                   "estados_embarazos_id": "2",
     *                   "derechohabientes_id": "3",
     *                   "municipios_id": "3",
     *                   "localidades_id": "420"
     *                },
     *                "acompaniantes": {
     *                   "id": 578,
     *                   "personas_id": "asdsadasd78",
     *                   "parentescos_id": "10",
     *                   "esResponsable": 1,
     *                   "personas": {
     *                      "id": "asdsadasd78",
     *                      "nombre": "Luis",
     *                      "paterno": "Valdez",
     *                      "materno": "Lescieur",
     *                      "domicilio": "Conocido",
     *                      "telefono": "965485232"
     *                },
     *             }
     *          ],
     *          "movimientos_incidencias": [
     *             {
     *                "id": 412,
     *                "incidencias_id": "1812201716028804",
     *                "turnos_id": "3",
     *                "ubicaciones_pacientes_id": "6",
     *                "estados_pacientes_id": "1",
     *                "triage_colores_id": "2",
     *                "subcategorias_cie10_id": 7354,
     *                "medico_reporta_id": null,
     *                "indicaciones": null,
     *                "reporte_medico": null,
     *                "diagnostico_egreso": null,
     *                "observacion_trabajo_social": null,
     *                "metodos_planificacion_id": null,
     *                "antiguedad": "4D 21hrs 55mins "
     *             }
     *         ],
     *         "referencias": [
     *             {
     *                "id": "",
     *                "medico_refiere_id": "medina",
     *                "diagnostico": "asdfsadf",
     *                "resumen_clinico": "rwerwe",
     *                "clues_origen": "CSCRO000015",
     *                "clues_destino": "CSSSA019954",
     *                "multimedias": {
     *                   "img": []
     *                },
     *                "esContrareferencia": 0
     *             }
     *         ],
     *         "altas_incidencias": []
     *     }
     **
     */
    public function update($id){
        $datos = Request::json()->all();

        $validacion = $this->ValidarParametros("", $id, $datos);
        if($validacion != ""){
            return Response::json(['error' => $validacion], HttpResponse::HTTP_CONFLICT);
        }

        DB::beginTransaction();
        try{
            $data = Incidencias::find($id);

            $this->AgregarDatos($datos, $data);
            $success = true;
        }catch (\Exception $e){
            return Response::json($e->getMessage(), 500);
        }
        if ($success){
            DB::commit();
            return Response::json(array("status" => 200, "messages" => "Operación realizada con exito", "data" => $data), 200);
        }else {
            DB::rollback();
            return Response::json(array("status" => 304, "messages" => "No modificado"),304);
        }
    }

    /**
     * @api {destroy} /incidencias/:id 5.Elimina Incidencia
     * @apiVersion 1.0.0
     * @apiName DestroyIncidencia
     * @apiGroup Transaccion/IncidenciaController
     * @apiPermission Admin
     *
     * @apiDescription Actualiza una Incidencia.
     *
     * @apiParam {number} id de la Incidencia que se quiere editar.
     **
     */
    public function destroy($id)
    {
        $success = false;
        DB::beginTransaction();
        try {
            $data = Incidencias::find($id);
            if($data)
                $data->delete();
            $success = true;
        }catch (\Exception $e){
            return Response::json($e->getMessage(), 500);
        }
        if ($success){
            DB::commit();
            return Response::json(array("status" => 200, "messages" => "Operación realizada con exito","data" => $data), 200);
        }else {
            DB::rollback();
            return Response::json(array("status" => 404, "messages" => "No se encontro el registro"), 404);
        }
    }

    /**
     * @api /incidencias 6.ValidarParametros
     * @apiVersion 1.0.0
     * @apiName IncidenciaValidarParametros
     * @apiGroup Transaccion/IncidenciaController
     * @apiPermission Admin
     *
     * @apiDescription Metodo que valida los parametros.
     *
     * @apiParam {json} request datos del request a validar.
     * @apiParamExample {json} Request-Ejemplo:
     *     {
     *        "id": "1812201716028804",
     *        "motivo_ingreso": "sdfsfsdf",
     *        "impresion_diagnostica": "sdfsdfsdfsdfsdfsdf",
     *        "clues": "CSSSA019954",
     *        "estados_incidencias_id": 1,
     *        "tieneReferencia": "",
     *        "pacientes": [
     *             {
     *                "id": 549,
     *                "personas_id": "jsiaojdiknaskldna88980",
     *                "personas_id_viejo": "",
     *                "personas": {
     *                   "id": "jsiaojdiknaskldna88980",
     *                   "nombre": "pruebaaaa",
     *                   "paterno": "pruebaaaa",
     *                   "materno": "pruebaaaa",
     *                   "domicilio": "adadsadsadsa",
     *                   "fecha_nacimiento": "1990-02-15",
     *                   "telefono": "965485232",
     *                   "estados_embarazos_id": "2",
     *                   "derechohabientes_id": "3",
     *                   "municipios_id": "3",
     *                   "localidades_id": "420"
     *                },
     *                "acompaniantes": {
     *                   "id": 578,
     *                   "personas_id": "asdsadasd78",
     *                   "parentescos_id": "10",
     *                   "esResponsable": 1,
     *                   "personas": {
     *                      "id": "asdsadasd78",
     *                      "nombre": "Luis",
     *                      "paterno": "Valdez",
     *                      "materno": "Lescieur",
     *                      "domicilio": "Conocido",
     *                      "telefono": "965485232"
     *                },
     *             }
     *          ],
     *          "movimientos_incidencias": [
     *             {
     *                "id": 412,
     *                "incidencias_id": "1812201716028804",
     *                "turnos_id": "3",
     *                "ubicaciones_pacientes_id": "6",
     *                "estados_pacientes_id": "1",
     *                "triage_colores_id": "2",
     *                "subcategorias_cie10_id": 7354,
     *                "medico_reporta_id": null,
     *                "indicaciones": null,
     *                "reporte_medico": null,
     *                "diagnostico_egreso": null,
     *                "observacion_trabajo_social": null,
     *                "metodos_planificacion_id": null,
     *                "antiguedad": "4D 21hrs 55mins "
     *             }
     *         ],
     *         "referencias": [
     *             {
     *                "id": "",
     *                "medico_refiere_id": "medina",
     *                "diagnostico": "asdfsadf",
     *                "resumen_clinico": "rwerwe",
     *                "clues_origen": "CSCRO000015",
     *                "clues_destino": "CSSSA019954",
     *                "multimedias": {
     *                   "img": []
     *                },
     *                "esContrareferencia": 0
     *             }
     *         ],
     *         "altas_incidencias": []
     *     }
     *
     * @apiSuccess {json} data datos del objeto que se va a crear.
     * @apiSuccessExample {json} Success-Response:
     *     {
     *        "data": {
     *           ...
     *        }
     *     }
     *
     * @apiError {json} error respuesta con errores.
     * @apiErrorExample {json} Respuesta Errores-Ejemplo
     *     {
     *        "error": {
     *           "nombre": [
     *              "unique"
     *           ]
     *        },
     *        "code": 409
     *     }
     *
     */
    private function ValidarParametros($key, $id, $request){

        $messages = [
            'required' => 'required',
            'unique' => 'unique'
        ];

        $rules = [
            //'id' => 'required|unique:incidencias,id,'.$id.',id,deleted_at,NULL',
            'motivo_ingreso' => 'required',
            'impresion_diagnostica' => 'required',
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

    private function obtenerAntiguedad($df) {

        $str = '';
        $str .= ($df->invert == 1) ? ' - ' : '';
        if ($df->y > 0) {   // years
            $str .= ($df->y > 1) ? $df->y . 'Y ' : $df->y . 'Y ';
        } if ($df->m > 0) {  // month
            $str .= ($df->m > 1) ? $df->m . 'M ' : $df->m . 'M ';
        } if ($df->d > 0) {  // days
            $str .= ($df->d > 1) ? $df->d . 'D ' : $df->d . 'D ';
        } if ($df->h > 0) {  // hours
            $str .= ($df->h > 1) ? $df->h . 'hrs ' : $df->h . 'hrs ';
        } if ($df->i > 0) {  // minutes
            $str .= ($df->i > 1) ? $df->i . 'mins ' : $df->i . 'mins ';
        }

        return $str;
    }

    /**
     * @api /incidencias 7.AgregarDatos
     * @apiVersion 1.0.0
     * @apiName IncidenciaAgregarDatos
     * @apiGroup Transaccion/IncidenciaController
     * @apiPermission Admin
     *
     * @apiDescription Metodo que agrega datos.
     *
     * @apiParam {json} data datos del Modelo.
     * @apiParam {json} datos json con datos agregar.
     * @apiParamExample {json} Request-Ejemplo:
     *     {
     *        "id": "1812201716028804",
     *        "motivo_ingreso": "sdfsfsdf",
     *        "impresion_diagnostica": "sdfsdfsdfsdfsdfsdf",
     *        "clues": "CSSSA019954",
     *        "estados_incidencias_id": 1,
     *        "tieneReferencia": "",
     *        "pacientes": [
     *             {
     *                "id": 549,
     *                "personas_id": "jsiaojdiknaskldna88980",
     *                "personas_id_viejo": "",
     *                "personas": {
     *                   "id": "jsiaojdiknaskldna88980",
     *                   "nombre": "pruebaaaa",
     *                   "paterno": "pruebaaaa",
     *                   "materno": "pruebaaaa",
     *                   "domicilio": "adadsadsadsa",
     *                   "fecha_nacimiento": "1990-02-15",
     *                   "telefono": "965485232",
     *                   "estados_embarazos_id": "2",
     *                   "derechohabientes_id": "3",
     *                   "municipios_id": "3",
     *                   "localidades_id": "420"
     *                },
     *                "acompaniantes": {
     *                   "id": 578,
     *                   "personas_id": "asdsadasd78",
     *                   "parentescos_id": "10",
     *                   "esResponsable": 1,
     *                   "personas": {
     *                      "id": "asdsadasd78",
     *                      "nombre": "Luis",
     *                      "paterno": "Valdez",
     *                      "materno": "Lescieur",
     *                      "domicilio": "Conocido",
     *                      "telefono": "965485232"
     *                },
     *             }
     *          ],
     *          "movimientos_incidencias": [
     *             {
     *                "id": 412,
     *                "incidencias_id": "1812201716028804",
     *                "turnos_id": "3",
     *                "ubicaciones_pacientes_id": "6",
     *                "estados_pacientes_id": "1",
     *                "triage_colores_id": "2",
     *                "subcategorias_cie10_id": 7354,
     *                "medico_reporta_id": null,
     *                "indicaciones": null,
     *                "reporte_medico": null,
     *                "diagnostico_egreso": null,
     *                "observacion_trabajo_social": null,
     *                "metodos_planificacion_id": null,
     *                "antiguedad": "4D 21hrs 55mins "
     *             }
     *         ],
     *         "referencias": [
     *             {
     *                "id": "",
     *                "medico_refiere_id": "medina",
     *                "diagnostico": "asdfsadf",
     *                "resumen_clinico": "rwerwe",
     *                "clues_origen": "CSCRO000015",
     *                "clues_destino": "CSSSA019954",
     *                "multimedias": {
     *                   "img": []
     *                },
     *                "esContrareferencia": 0
     *             }
     *         ],
     *         "altas_incidencias": []
     *     }
     *
     *
     * @apiSuccess {json} data datos del objeto que se va a crear.
     * @apiSuccessExample {json} Success-Response:
     *     {
     *        "data": {
     *           ...
     *        }
     *     }
     *
     */
    private function AgregarDatos($datos, $data){

        $movimientos_incidencias = null;
        $altas_incidencias = null;
        $referencia = null;

        //Informacion de incidencia
        $data->id = $datos['id'];
        $data->motivo_ingreso = $datos['motivo_ingreso'];
        $data->impresion_diagnostica = $datos['impresion_diagnostica'];
        $data->estados_incidencias_id = $datos['estados_incidencias_id'];

        if ($data->save()){
            $datos = (object) $datos;
            //verificar si existe paciente, en caso de que exista proceder a guardarlo
            if(property_exists($datos, "pacientes")){
                //limpiar el arreglo de posibles nullos
                $detallePacientes = array_filter($datos->pacientes, function($v){return $v !== null;});
                //recorrer cada elemento del arreglo
                foreach ($detallePacientes as $key => $valuePaciente) {
                    //validar que el valor no sea null
                    if ($valuePaciente != null) {
                        //comprobar si el value es un array, si es convertirlo a object mas facil para manejar.
                        if (is_array($valuePaciente))
                            $valuePaciente = (object)$valuePaciente;

                        //si existe actualizar
                        if(property_exists($valuePaciente, "personas_id_viejo") && !$valuePaciente->personas_id_viejo == null){
                            $persona = Personas::find($valuePaciente->personas_id_viejo);
                        }else{
                            $persona = Personas::find($valuePaciente->personas_id);
                        }

                        if(property_exists($valuePaciente, "personas")){
                            //limpiar el arreglo de posibles nullos
                            $detallePersonas = array_filter($valuePaciente->personas, function($v){return $v !== null;});
                            if (is_array($detallePersonas))
                                $detallePersonas = (object)$detallePersonas;

                            //si no existe crear
                            if (!$persona)
                                $persona = new Personas;

                            $persona->id                    = $valuePaciente->personas_id;
                            $persona->nombre                = $detallePersonas->nombre;
                            $persona->paterno               = $detallePersonas->paterno;
                            $persona->materno               = $detallePersonas->materno;
                            $persona->domicilio             = $detallePersonas->domicilio;
                            $persona->fecha_nacimiento      = $detallePersonas->fecha_nacimiento;
                            $persona->telefono              = $detallePersonas->telefono;
                            $persona->estados_embarazos_id  = $detallePersonas->estados_embarazos_id;
                            $persona->derechohabientes_id   = $detallePersonas->derechohabientes_id;
                            $persona->municipios_id         = $detallePersonas->municipios_id;
                            $persona->localidades_id        = $detallePersonas->localidades_id;

                            if ($persona->save()) {
                                //si existe actualizar
                                if (property_exists($detallePersonas, "id")) {
                                    if(!$valuePaciente->id == null || !$valuePaciente->id == ""){
                                        $paciente = Pacientes::find($valuePaciente->id);
                                    }else{
                                        if(property_exists($valuePaciente, "personas_id_viejo") && !$valuePaciente->personas_id_viejo == null){
                                            $paciente = Pacientes::where('personas_id', $valuePaciente->personas_id_viejo)->first();
                                        }else
                                            $paciente = new Pacientes;
                                    }
                                }

                                $paciente->personas_id = $persona->id;

                                if ($paciente->save()) {
                                    if ($valuePaciente->id == null || $valuePaciente->id == "") {
                                        DB::insert("insert into incidencia_clue (incidencias_id, clues) VALUE ('$data->id', '$datos->clues')");
                                        DB::insert("insert into incidencia_paciente (incidencias_id, pacientes_id) VALUE ('$data->id', '$paciente->id')");
                                    }else{
                                        //DB::update("update incidencia_clue set clues = '$datos->clues' where incidencias_id = '$data->id' and motivo_ingreso = '$data->motivo_ingreso' and impresion_diagnostica = '$data->impresion_diagnostica' ");
                                    }
                                }
                            }
                        }

                        if(property_exists($valuePaciente, "acompaniantes")){
                            //limpiar el arreglo de posibles nullos
                            $detalleAcompaniantes = array_filter($valuePaciente->acompaniantes, function($v){return $v !== null;});
                            //recorrer cada elemento del arreglo
                            foreach ($detalleAcompaniantes as $key => $valueAcompaniante) {
                                //validar que el valor no sea null
                                if($valueAcompaniante != null){
                                    //comprobar si el value es un array, si es convertirlo a object mas facil para manejar.
                                    if(is_array($valueAcompaniante))
                                        $valueAcompaniante = (object) $valueAcompaniante;

                                    //si existe actualizar esta linea donde pones id le podes poner con find cual es la linea que no guarda
                                    $personaA = Personas::find($valueAcompaniante->personas_id);

                                    if(property_exists($valueAcompaniante, "personas")){
                                        //limpiar el arreglo de posibles nullos
                                        $detallePersonaA = array_filter($valueAcompaniante->personas, function($v){return $v !== null;});

                                        if (is_array($detallePersonaA))
                                            $detallePersonaA = (object)$detallePersonaA;
                                        //si no existe crear
                                        if(!$personaA)
                                            $personaA = new Personas;

                                        $personaA->id                 = $valueAcompaniante->personas_id;
                                        $personaA->nombre             = $detallePersonaA->nombre;
                                        $personaA->paterno            = $detallePersonaA->paterno;
                                        $personaA->materno            = $detallePersonaA->materno;
                                        $personaA->telefono           = $detallePersonaA->telefono;
                                        $personaA->domicilio          = $detallePersonaA->domicilio;

                                        if ($personaA->save()){
                                            if(!$valueAcompaniante->id == null || !$valueAcompaniante->id == ""){
                                                $acompaniante = Acompaniantes::find($valueAcompaniante->id);
                                            }else{
                                                $acompaniante = Acompaniantes::where("personas_id", $personaA->id)->where("parentescos_id", $valueAcompaniante->parentescos_id)->where("esResponsable", $valueAcompaniante->esResponsable)->first();
                                                if (!$acompaniante){
                                                    $acompaniante = new Acompaniantes;
                                                }
                                            }

                                            $acompaniante->personas_id      = $personaA->id;
                                            $acompaniante->parentescos_id   = $valueAcompaniante->parentescos_id;
                                            $acompaniante->esResponsable    = $valueAcompaniante->esResponsable;

                                            if($acompaniante->save()){
                                                if($valuePaciente->id == null || $valuePaciente->id == ""){
                                                    DB::insert("insert into acompaniante_paciente (pacientes_id, acompaniantes_id) VALUE ('$paciente->id', '$acompaniante->id')");
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            //verificar si existe altas_incidencias, en caso de que exista proceder a guardarlo
            if(property_exists($datos, "altas_incidencias")){
                //limpiar el arreglo de posibles nullos
                $detalleAltas = array_filter($datos->altas_incidencias, function($v){return $v !== null;});
                if(is_array($detalleAltas))
                    $detalleAltas = (object) $detalleAltas;

                //borrar los datos previos de articulo para no duplicar información
                if(property_exists($detalleAltas, "id")){
                    AltasIncidencias::where("id", $detalleAltas->id)->where("incidencias_id", $data->id)->delete();
                }

                //recorrer cada elemento del arreglo
                foreach ($detalleAltas as $key => $value) {
                    //validar que el valor no sea null
                    if($value != null){
                        //comprobar si el value es un array, si es convertirlo a object mas facil para manejar.
                        if(is_array($value))
                            $value = (object) $value;
                        //comprobar que el dato que se envio no exista o este borrado, si existe y esta borrado poner en activo nuevamente
                        if(property_exists($value, "id")){
                            DB::update("update altas_incidencias set deleted_at = null where id = '$value->id' and incidencias_id = '$data->id' ");
                            //si existe actualizar
                            $altas_incidencias = AltasIncidencias::where("id", $value->id)->where("incidencias_id", $data->id)->first();
                        }else
                            $altas_incidencias = new AltasIncidencias;

                        $altas_incidencias->incidencias_id                  = $data->id;

                        $altas_incidencias->medico_reporta_id               = $value->medico_reporta_id;
                        $altas_incidencias->metodos_planificacion_id        = $value->metodos_planificacion_id;
                        $altas_incidencias->tipos_altas_id                  = $value->tipos_altas_id;
                        $altas_incidencias->turnos_id                       = $value->turnos_id;

                        $altas_incidencias->diagnostico_egreso              = $value->diagnostico_egreso;
                        $altas_incidencias->observacion_trabajo_social      = $value->observacion_trabajo_social;

                        $altas_incidencias->clues_contrarefiere              = $value->clues_contrarefiere;
                        $altas_incidencias->clues_regresa                    = $value->clues_regresa;
                        $altas_incidencias->resumen_clinico                  = $value->resumen_clinico;
                        $altas_incidencias->instrucciones_recomendaciones    = $value->instrucciones_recomendaciones;

                        if($altas_incidencias->save()){
                            if(property_exists($value, "visitas_puerperales")){
                                //limpiar el arreglo de posibles nullos
                                $detalleVisitas = array_filter($value->visitas_puerperales, function($v){return $v !== null;});
                                if(is_array($detalleVisitas))
                                    $detalleVisitas = (object) $detalleVisitas;

                                //borrar los datos previos de articulo para no duplicar información
                                if(property_exists($detalleVisitas, "id")){
                                    VisitasPuerperales::where("altas_incidencias_id", $value->id)->delete();
                                }

                                foreach ($detalleVisitas as $key => $valueVisita) {
                                    //comprobar si el value es un array, si es convertirlo a object mas facil para manejar.
                                    if(is_array($valueVisita))
                                        $valueVisita = (object) $valueVisita;

                                    //comprobar que el dato que se envio no exista o este borrado, si existe y esta borrado poner en activo nuevamente
                                    if(!$valueVisita->id == null || !$valueVisita->id == ""){
                                        DB::update("update visitas_puerperales set deleted_at = null where id = '$valueVisita->id' and altas_incidencias_id = '$value->id' ");
                                        //si existe actualizar
                                        $visita_puerperio = VisitasPuerperales::where("id", $valueVisita->id)->where("altas_incidencias_id", $value->id)->first();
                                    }else
                                        $visita_puerperio = new VisitasPuerperales;

                                    $visita_puerperio->altas_incidencias_id            = $value->id;

                                    $visita_puerperio->fecha_visita                    = $valueVisita->fecha_visita;
                                    $visita_puerperio->seAtendio                       = $valueVisita->seAtendio;
                                    $visita_puerperio->porque                          = $valueVisita->porque;
                                    $visita_puerperio->observaciones                   = $valueVisita->observaciones;

                                    $visita_puerperio->save();
                                }
                            }
                        }

                    }
                }

            }

        }
    }
}
