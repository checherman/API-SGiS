<?php

namespace App\Http\Controllers\V1\Transacciones;

use App\Events\NotificacionEvent;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DateTime;
use Illuminate\Http\Response as HttpResponse;


use Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use \Validator,\Hash, \Response, \DB;
use Illuminate\Support\Facades\Input;

use App\Models\Catalogos\Clues;
use App\Models\Sistema\Multimedias;
use App\Models\Sistema\SisUsuario;
use App\Models\Transacciones\AltasIncidencias;

use App\Models\Transacciones\Incidencias;
use App\Models\Transacciones\Pacientes;
use App\Models\Transacciones\Responsables;
use App\Models\Transacciones\Personas;
use App\Models\Transacciones\Acompaniantes;
use App\Models\Transacciones\MovimientosIncidencias;
use App\Models\Transacciones\Referencias;

/**
 * Controlador Incidencia
 *
 * @package    UGUS API
 * @subpackage Controlador
 * @author     Luis Alberto Valdez Lescieur <luisvl13@gmail.com>
 * @created    2017-05-25
 *
 * Controlador `Incidencia`: Controlador  para el manejo de incidencias
 *
 */
class IncidenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
                if(!$edoIncidencia == null) {
                    $data = Incidencias::select("incidencias.*")->with("pacientes.personas", "pacientes.acompaniantes.personas")
                        ->with("movimientos_incidencias", "referencias", "altas_incidencias", "estados_incidencias")
                        ->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
                        ->where('incidencia_clue.clues',$cluesH)
                        ->orderBy($order, $orden);
                }else{
                    $data = Incidencias::select("incidencias.*")->with("pacientes.personas", "pacientes.acompaniantes.personas")
                        ->with("movimientos_incidencias", "referencias", "altas_incidencias", "estados_incidencias")
                        ->join('estados_incidencias', 'estados_incidencias.id', '=', 'incidencias.estados_incidencias_id')
                        ->where('incidencias.estados_incidencias_id',$edoIncidencia)
                        ->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
                        ->where('incidencia_clue.clues',$cluesH)
                        ->orderBy($order, $orden);
                }

                $search = trim($valor);
                $keyword = $search;
                $data = $data->whereNested(function($query) use ($keyword){
                    $query->where("id", "LIKE", '%'.$keyword.'%');
                });

                $total = $data->get();
                $data = $data->skip($pagina-1)->take($datos['limite'])->get();
            }
            else{
                if(!$edoIncidencia == null){
                    $data = Incidencias::select("incidencias.*")->with("pacientes.personas", "pacientes.acompaniantes.personas")
                        ->with("movimientos_incidencias", "referencias", "altas_incidencias", "estados_incidencias")
                        ->join('estados_incidencias', 'estados_incidencias.id', '=', 'incidencias.estados_incidencias_id')
                        ->where('incidencias.estados_incidencias_id',$edoIncidencia)
                        ->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
                        ->where('incidencia_clue.clues',$cluesH)
                        ->skip($pagina-1)->take($datos['limite'])->orderBy('incidencias.id', $orden)
                        ->get();

                    $total = Incidencias::all();
                }else{
                    $data = Incidencias::select("incidencias.*")->with("pacientes.personas", "pacientes.acompaniantes.personas")
                        ->with("movimientos_incidencias", "referencias", "altas_incidencias", "estados_incidencias")
                        ->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
                        ->where('incidencia_clue.clues',$cluesH)
                        ->skip($pagina-1)->take($datos['limite'])->orderBy($order, $orden)
                        ->get();

                    $total = Incidencias::all();
                }
            }

        }
        else{
            $data = Incidencias::with("pacientes.personas", "pacientes.acompaniantes.personas")
                ->with("movimientos_incidencias", "referencias", "altas_incidencias", "estados_incidencias")
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

        foreach($data as $mov) {
            if (!in_array($mov->estados_incidencias['nombre'], $estadosIncidencias)) {
                array_push($estadosIncidencias, ['id' => $mov->estados_incidencias['id'], 'nombre' => $mov->estados_incidencias['nombre']]);
            }
        }

        $estadosIncidencias = array_map("unserialize", array_unique(array_map("serialize", $estadosIncidencias)));

        $data[count($data)] = array("estados_incidencias" => array_values($estadosIncidencias));
        //$data[count($data)] = array("estados_incidencias" => $estadosIncidencias);

        if(!$data){
            return Response::json(array("status" => 404,"messages" => "No hay resultados"), 404);
        }
        else{
            return Response::json(array("status" => 200,"messages" => "Operación realizada con exito","data" => $data,"total" => count($total)), 200);

        }


    }

    /**
     * Store a newly created resource in storage.
     * @return HttpResponse
     * @internal param \Illuminate\Http\Request|Request $request
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
     * Devuelve la información del registro especificado.
     *
     * @param  int  $id que corresponde al identificador del recurso a mostrar
     *
     * @return Response
     * <code style="color:green"> Respuesta Ok json(array("status": 200, "messages": "Operación realizada con exito", "data": array(resultado)),status) </code>
     * <code> Respuesta Error json(array("status": 404, "messages": "No hay resultados"),status) </code>
     */
    public function show($id){
        $data = Incidencias::where('id',$id)
            ->with("pacientes.personas", "pacientes.acompaniantes.personas")
            ->with("movimientos_incidencias")
            ->with("referencias")
            ->with("altas_incidencias")
            ->with("estados_incidencias")
            ->first();

        if(!$data){
            return Response::json(array("status" => 204,"messages" => "No hay resultados"), 204);
        }
        else{

            $clues = DB::table('incidencia_clue')->where('incidencias_id', $id)->first();
            $data->clues = $clues->clues;

            foreach ($data->movimientos_incidencias as $key => $value) {
                $ahora = new DateTime("Now");
                $created_at = $value->created_at;
                $diff = $created_at->diff($ahora);
                $antiguedad = $this->obtenerAntiguedad($diff);

                $value->antiguedad = $antiguedad;
            }

            return Response::json(array("status" => 200,"messages" => "Operación realizada con exito","data" => $data), 200);
        }
    }

    /**
     * Actualizar el  registro especificado en la base de datos
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
            $data = Incidencias::find($id);
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
                        $persona = Personas::find($valuePaciente->personas_id);

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
                            $persona->localidades_id        = $detallePersonas->localidades_id;

                            if ($persona->save()) {
                                //si existe actualizar
                                if (property_exists($detallePersonas, "id")) {
                                    if(!$valuePaciente->id == null || !$valuePaciente->id == ""){
                                        $paciente = Pacientes::find($valuePaciente->id);
                                    }else
                                        $paciente = new Pacientes;
                                } else
                                    $paciente = new Pacientes;

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
                                            //si existe actualizar
                                            if(property_exists($valueAcompaniante, "id")) {
                                                if(!$valueAcompaniante->id == null || !$valueAcompaniante->id == ""){
                                                    $acompaniante = Acompaniantes::find($valueAcompaniante->id);
                                                }else
                                                    $acompaniante = new Acompaniantes;
                                            }else
                                                $acompaniante = new Acompaniantes;

                                            $acompaniante->personas_id      = $personaA->id;
                                            $acompaniante->parentescos_id   = $valueAcompaniante->parentescos_id;
                                            $acompaniante->esResponsable    = $valueAcompaniante->esResponsable;

                                            if($acompaniante->save()){
                                                if($valueAcompaniante->id == null || $valueAcompaniante->id == ""){
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

            //verificar si existe movimientos_incidencias, en caso de que exista proceder a guardarlo
            if(property_exists($datos, "movimientos_incidencias")){
                //limpiar el arreglo de posibles nullos
                $detalleMovimientos = array_filter($datos->movimientos_incidencias, function($v){return $v !== null;});
                if(is_array($detalleMovimientos))
                    $detalleMovimientos = (object) $detalleMovimientos;
                //borrar los datos previos de articulo para no duplicar información
                if(property_exists($detalleMovimientos, "id")){
                    MovimientosIncidencias::where("id", $detalleMovimientos->id)->where("incidencias_id", $data->id)->delete();
                }

                //recorrer cada elemento del arreglo
                foreach ($detalleMovimientos as $key => $value) {
                    //validar que el valor no sea null
                    if($value != null){
                        //comprobar si el value es un array, si es convertirlo a object mas facil para manejar.
                        if(is_array($value))
                            $value = (object) $value;
                        //comprobar que el dato que se envio no exista o este borrado, si existe y esta borrado poner en activo nuevamente
                        if(property_exists($value, "id")){
                            if(!$value->id == "" || !$value->id == null){
                                DB::update("update movimientos_incidencias set deleted_at = null where id = '$value->id' and incidencias_id = '$data->id' ");
                                //si existe actualizar
                                $movimientos_incidencias = MovimientosIncidencias::where("id", $value->id)->where("incidencias_id", $data->id)->first();
                            }else
                                $movimientos_incidencias = new MovimientosIncidencias;

                        }else
                            $movimientos_incidencias = new MovimientosIncidencias;


                        $movimientos_incidencias->incidencias_id                  = $data->id;
                        $movimientos_incidencias->medico_reporta_id               = $value->medico_reporta_id;
                        $movimientos_incidencias->indicaciones                    = $value->indicaciones;
                        $movimientos_incidencias->reporte_medico                  = $value->reporte_medico;

                        $movimientos_incidencias->estados_pacientes_id            = $value->estados_pacientes_id;
                        $movimientos_incidencias->ubicaciones_pacientes_id        = $value->ubicaciones_pacientes_id;
                        $movimientos_incidencias->triage_colores_id               = $value->triage_colores_id;
                        $movimientos_incidencias->subcategorias_cie10_id          = $value->subcategorias_cie10_id;
                        $movimientos_incidencias->turnos_id                       = $value->turnos_id;

                        $movimientos_incidencias->save();
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
                        $altas_incidencias->diagnostico_egreso              = $value->diagnostico_egreso;
                        $altas_incidencias->observacion_trabajo_social      = $value->observacion_trabajo_social;
                        $altas_incidencias->metodos_planificacion_id        = $value->metodos_planificacion_id;

                        $altas_incidencias->ubicaciones_pacientes_id        = $value->ubicaciones_pacientes_id;
                        $altas_incidencias->turnos_id                       = $value->turnos_id;

                        if($altas_incidencias->save()){
                            //comprobar que el dato que se envio no exista
                            if(property_exists($value, "id")) {
                                //si existe actualizar
                                $multimedia = Multimedias::where("id", $value->id)->where("refererencias_id", $data->id)->first();
                            }else
                                $multimedia = new Multimedias;

                            $multimedia->altas_incidencias_id                   = $altas_incidencias->id;
                            $multimedia->tipo                                   = "imagen";
                            $multimedia->url                                    = $this->convertir_imagen($value->img, 'referencias', $data->id);

                            $multimedia->save();
                        }

                    }
                }

            }

            //verificar si existe referencias, en caso de que exista proceder a guardarlo
            if(property_exists($datos, "referencias")){
                //limpiar el arreglo de posibles nullos
                $detalleReferencia = array_filter($datos->referencias, function($v){return $v !== null;});
                //dd($detalleMovimientos);
                if(is_array($detalleReferencia))
                    $detalleReferencia = (object) $detalleReferencia;
                //borrar los datos previos de articulo para no duplicar información
                if(property_exists($detalleReferencia, "id")){
                    Referencias::where("id", $detalleReferencia->id)->where("incidencias_id", $data->id)->delete();
                }

                //recorrer cada elemento del arreglo
                foreach ($detalleReferencia as $key => $valueReferencia) {
                    //validar que el valor no sea null
                    if($valueReferencia != null){
                        //comprobar si el value es un array, si es convertirlo a object mas facil para manejar.
                        if(is_array($valueReferencia))
                            $valueReferencia = (object) $valueReferencia;

                        //comprobar que el dato que se envio no exista o este borrado, si existe y esta borrado poner en activo nuevamente
                        if(property_exists($valueReferencia, "id")) {
                            DB::update("update referencias set deleted_at = null where id = '$valueReferencia->id' and incidencias_id = '$data->id' ");
                            //si existe actualizar
                            $referencia = Referencias::where("id", $valueReferencia->id)->where("incidencias_id", $data->id)->first();
                        }else
                            $referencia = new Referencias;

                        $referencia->incidencias_id                 = $data->id;
                        $referencia->medico_refiere_id              = $valueReferencia->medico_refiere_id;
                        $referencia->diagnostico                    = $valueReferencia->diagnostico;
                        $referencia->resumen_clinico                = $valueReferencia->resumen_clinico;
                        $referencia->clues_origen                   = $valueReferencia->clues_origen;
                        $referencia->clues_destino                  = $valueReferencia->clues_destino;

                        if($referencia->save()){

                            if(property_exists($valueReferencia, "multimedias")){
                                $medios = array_filter($valueReferencia->multimedias, function($v){return $v !== null;});

                                Multimedias::where("id", $value->id)->where("referencias_id", $data->id)->delete();
                                foreach ($medios as $key => $value) {
                                    $value = (object) $value;
                                    if($value != null){
                                        DB::update("update multimedias set deleted_at = null where sis_usuarios_id = $data->id and valor = '$value->valor' ");
                                        $multimedia = Multimedias::where("sis_usuarios_id", $data->id)->where("valor", $value->valor)->first();

                                        if(!$multimedia)
                                            $multimedia = new Multimedias;

                                        $multimedia->referencias_id                   = $referencia->id;
                                        $multimedia->tipo                             = "imagen";
                                        $multimedia->url                              = $this->convertir_imagen($value->img, 'referencias', $data->id);

                                        $multimedia->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $obj =  JWTAuth::parseToken()->getPayload();
            $usuario = SisUsuario::where("email", $obj->get('email'))->first();

            $mensaje = collect();

            if(!$movimientos_incidencias == null){
                $mensaje->put('titulo', "Nueva atencion del paciente ... ");
                $mensaje->put('mensaje', $usuario->nombre." reporto una atencion del folio ". $data->id);
                $mensaje->put('created_at', date('Y-m-d H:i:s'));
                $mensaje->put('enviado', null);
                $mensaje->put('leido', null);

                $mensaje->put('sis_usuarios_id', ['ids','ids']);
                $mensaje->put('movimientos_incidencias', $movimientos_incidencias);
            }

            if(!$referencia == null){
                $cluesOrigen = Clues::where("clues", $referencia->clues_origen)->first();
                $cluesDestino = Clues::where("clues", $referencia->clues_destino)->first();

                $mensaje->put('titulo', "Nueva referencia del paciente ... ");
                $mensaje->put('mensaje', $usuario->nombre." realizo una referencia del folio ". $data->id. " de (" . $cluesOrigen->clues . ")-" . $cluesOrigen->nombre  . " a (". $cluesOrigen->clues . ")-" . $cluesOrigen->nombre);
                $mensaje->put('created_at', date('Y-m-d H:i:s'));
                $mensaje->put('enviado', null);
                $mensaje->put('leido', null);

                $mensaje->put('sis_usuarios_id', ['ids','ids']);
                $mensaje->put('referencias', $referencia);
            }

            if(!$altas_incidencias == null){
                $mensaje->put('titulo', "Alta del paciente ... ");
                $mensaje->put('mensaje', $usuario->nombre." reporto una atencion del folio ". $data->id);
                $mensaje->put('created_at', date('Y-m-d H:i:s'));
                $mensaje->put('enviado', null);
                $mensaje->put('leido', null);

                $mensaje->put('sis_usuarios_id', ['ids','ids']);
                $mensaje->put('altas_incidencias', $altas_incidencias);
            }

            event(new NotificacionEvent($mensaje, $usuario));
        }
    }

    /**
     * Actualizar el  registro especificado en el la base de datos
     *
     * <h4>Request</h4>
     * Recibe un Input Request con el json de los datos
     *
     * @param $data
     * @param $nombre
     * @param $i
     * @return Response <code style="color:green"> Respuesta Ok json(array("status": 200, "messages": "Operación realizada con exito", "data": array(resultado)),status) </code>
     * <code style="color:green"> Respuesta Ok json(array("status": 200, "messages": "Operación realizada con exito", "data": array(resultado)),status) </code>
     * <code> Respuesta Error json(array("status": 304, "messages": "No modificado"),status) </code>
     * @internal param int $id que corresponde al identificador del dato a actualizar
     */
    public function convertir_imagen($data, $nombre, $i){
        try{
            $data = base64_decode($data);

            $im = imagecreatefromstring($data);

            if ($im !== false) {
                $time = time().rand(11111, 99999);
                $name = $nombre.$i."_".$time.".jpeg";
                header('Content-Type: image/pjpeg');
                imagejpeg($im, public_path() ."/adjunto/".$nombre."/".$name);
                imagedestroy($im);
                return $name;
            }
            else {
                return null;
            }
        }catch (\Exception $e) {

            return \Response::json(["error" => $e->getMessage(), "nombre" => $nombre], 400);
        }
    }
}
