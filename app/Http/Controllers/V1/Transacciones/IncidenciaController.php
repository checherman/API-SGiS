<?php

namespace App\Http\Controllers\V1\Transacciones;


use App\Http\Requests;
use App\Http\Controllers\Controller;
use DateTime;
use Illuminate\Http\Response as HttpResponse;


use Request;
use \Validator,\Hash, \Response, \DB;
use Illuminate\Support\Facades\Input;

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


        $parametros = Input::only('q','page','per_page','clues','edo_incidencia');

        if ($parametros['q']) {
            if ($parametros['clues']) {
                if ($parametros['edo_incidencia']) {//CLUES E INCIDENCIA
                    $data = Incidencias::select('incidencias.*')
                        ->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
                        ->where('incidencia_clue.clues', $parametros['clues'])
                        ->with("pacientes.personas", "pacientes.acompaniantes.personas")
                        ->with("movimientos_incidencias", "referencias", "estados_incidencias")
                        ->where('incidencias.estados_incidencias_id', $parametros['edo_incidencia'])
                        ->where(function ($query) use ($parametros) {
                            $query->where('id', 'LIKE', "%" . $parametros['q'] . "%")
                                ->orWhere('nombre', 'LIKE', "%" . $parametros['q'] . "%");
                        });
                }else{//CLUES
                    $data = Incidencias::select('incidencias.*')
                        ->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
                        ->where('incidencia_clue.clues', $parametros['clues'])
                        ->with("pacientes.personas", "pacientes.acompaniantes.personas")
                        ->with("movimientos_incidencias", "referencias", "estados_incidencias")
                        ->where(function ($query) use ($parametros) {
                            $query->where('id', 'LIKE', "%" . $parametros['q'] . "%")
                                ->orWhere('nombre', 'LIKE', "%" . $parametros['q'] . "%");
                        });
                }
            }else{//NOCLUES
                if ($parametros['edo_incidencia']) {//NOCLUES E INCIDENCIA
                    $data = Incidencias::with("pacientes.personas", "pacientes.acompaniantes.personas")
                        ->with("movimientos_incidencias", "referencias", "estados_incidencias")
                        ->where('incidencias.estados_incidencias_id', $parametros['edo_incidencia'])
                        ->where(function ($query) use ($parametros) {
                            $query->where('id', 'LIKE', "%" . $parametros['q'] . "%")
                                ->orWhere('nombre', 'LIKE', "%" . $parametros['q'] . "%");
                        });
                }else{//NO CLUES NO INCIDENCIA
                    $data = Incidencias::with("pacientes.personas", "pacientes.acompaniantes.personas")
                        ->with("movimientos_incidencias", "referencias", "estados_incidencias")
                        ->where(function ($query) use ($parametros) {
                            $query->where('id', 'LIKE', "%" . $parametros['q'] . "%")
                                ->orWhere('nombre', 'LIKE', "%" . $parametros['q'] . "%");
                        });
                }
            }
        } else {//NOquery
            if ($parametros['clues']) {//CLUES
                if ($parametros['edo_incidencia']) {//CLUES E INCIDENCIA
                    $data = Incidencias::select('incidencias.*')
                        ->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
                        ->where('incidencia_clue.clues', $parametros['clues'])
                        ->with("pacientes.personas", "pacientes.acompaniantes.personas")
                        ->with("movimientos_incidencias", "referencias", "estados_incidencias")
                        ->where('incidencias.estados_incidencias_id', $parametros['edo_incidencia']);
                }else{//CLUES NOINCIDENCIA
                    $data = Incidencias::select('incidencias.*')
                        ->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
                        ->where('incidencia_clue.clues', $parametros['clues'])
                        ->with("pacientes.personas", "pacientes.acompaniantes.personas")
                        ->with("movimientos_incidencias", "referencias", "estados_incidencias");
                }
            }else{//NOCLUES
                if ($parametros['edo_incidencia']) {//NOCLUES E INCIDENCIA
                    $data = Incidencias::with("pacientes.personas", "pacientes.acompaniantes.personas")
                        ->with("movimientos_incidencias", "referencias", "estados_incidencias")
                        ->where('incidencias.estados_incidencias_id', $parametros['edo_incidencia']);
                }else{//NOCLUES NOINCIDENCIA
                    $data = Incidencias::with("pacientes.personas", "pacientes.acompaniantes.personas")
                        ->with("movimientos_incidencias", "referencias", "estados_incidencias");
                }
            }
        }

        if(isset($parametros['page'])){
            $resultadosPorPagina = isset($parametros["per_page"])? $parametros["per_page"] : 20;
            $data = $data->paginate($resultadosPorPagina);
        } else {
            $data = $data->get();
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

        $data[count($data)] = array("estados_incidencias" => $estadosIncidencias);

        return Response::json([ 'data' => $data],200);
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
            ->with("pacientes.personas","pacientes.acompaniantes.personas")
            ->with("movimientos_incidencias")
            ->with("referencias")
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
            'id' => 'required|unique:incidencias,id,'.$id.',id,deleted_at,NULL',
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
        if ($df->y > 0) {
            // years
            $str .= ($df->y > 1) ? $df->y . 'Y ' : $df->y . 'Y ';
        } if ($df->m > 0) {
            // month
            $str .= ($df->m > 1) ? $df->m . 'M ' : $df->m . 'M ';
        } if ($df->d > 0) {
            // days
            $str .= ($df->d > 1) ? $df->d . 'D ' : $df->d . 'D ';
        } if ($df->h > 0) {
            // hours
            $str .= ($df->h > 1) ? $df->h . 'hrs ' : $df->h . 'hrs ';
        } if ($df->i > 0) {
            // minutes
            $str .= ($df->i > 1) ? $df->i . 'mins ' : $df->i . 'mins ';
        }

        return $str;
    }

    private function AgregarDatos($datos, $data){

        //Informacion de incidencia
        $data->id = $datos['id'];
        $data->servidor_id = env("SERVIDOR_ID");
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
                        $persona = Personas::where("id", $valuePaciente->personas_id)->first();

                        if(property_exists($valuePaciente, "personas")){
                            //limpiar el arreglo de posibles nullos
                            $detallePersonas = array_filter($valuePaciente->personas, function($v){return $v !== null;});
                            if (is_array($detallePersonas))
                                $detallePersonas = (object)$detallePersonas;

                            //si no existe crear
                            if (!$persona)
                                $persona = new Personas;

                            $persona->id = $valuePaciente->personas_id;
                            $persona->servidor_id = env("SERVIDOR_ID");
                            $persona->nombre = $detallePersonas->nombre;
                            $persona->paterno = $detallePersonas->paterno;
                            $persona->materno = $detallePersonas->materno;
                            $persona->domicilio = $detallePersonas->domicilio;
                            $persona->fecha_nacimiento = $detallePersonas->fecha_nacimiento;
                            $persona->telefono = $detallePersonas->telefono;
                            $persona->estados_embarazos_id = $detallePersonas->estados_embarazos_id;
                            $persona->derechohabientes_id = $detallePersonas->derechohabientes_id;
                            $persona->localidades_id = $detallePersonas->localidades_id;

                            if ($persona->save()) {
                                //si existe actualizar
                                if (property_exists($detallePersonas, "id")) {
                                    $paciente = Pacientes::where("id", $valuePaciente->id)->first();
                                } else
                                    $paciente = new Pacientes;

                                $paciente->servidor_id = env("SERVIDOR_ID");
                                $paciente->personas_id = $persona->id;

                                if ($paciente->save()) {
                                    if (!property_exists($detallePersonas, "id")) {
                                        DB::insert("insert into incidencia_clue (incidencias_id, clues) VALUE ('$data->id', '$datos->clues')");
                                        DB::insert("insert into incidencia_paciente (incidencias_id, pacientes_id) VALUE ('$data->id', '$paciente->id')");
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

                                    //si existe actualizar
                                    $personaA = Personas::where('id', $valueAcompaniante->personas_id)->first();

                                    if(property_exists($valueAcompaniante, "personas")){
                                        //limpiar el arreglo de posibles nullos
                                        $detallePersonaA = array_filter($valueAcompaniante->personas, function($v){return $v !== null;});

                                        if (is_array($detallePersonaA))
                                            $detallePersonaA = (object)$detallePersonaA;
                                        //si no existe crear
                                        if(!$personaA)
                                            $personaA = new Personas;

                                        $personaA->servidor_id 	     = env("SERVIDOR_ID");
                                        $personaA->id                 = $valueAcompaniante->personas_id;
                                        $personaA->nombre             = $detallePersonaA->nombre;
                                        $personaA->paterno            = $detallePersonaA->paterno;
                                        $personaA->materno            = $detallePersonaA->materno;
                                        $personaA->telefono           = $detallePersonaA->telefono;
                                        $personaA->domicilio          = $detallePersonaA->domicilio;

                                        if ($personaA->save()){
                                            //si existe actualizar
                                            if(property_exists($valueAcompaniante, "id")) {
                                                $acompaniante = Acompaniantes::where("id", $valueAcompaniante->id)->first();
                                            }else
                                                $acompaniante = new Acompaniantes;

                                            $acompaniante->servidor_id 	    = env("SERVIDOR_ID");
                                            $acompaniante->personas_id      = $personaA->id;
                                            $acompaniante->parentescos_id   = $valueAcompaniante->parentescos_id;
                                            $acompaniante->esResponsable    = $valueAcompaniante->esResponsable;

                                            if($acompaniante->save()){
                                                if(!property_exists($valueAcompaniante, "id")){
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
                            DB::update("update movimientos_incidencias set deleted_at = null where id = '$value->id' and incidencias_id = '$data->id' ");
                            //si existe actualizar
                            $movimientos_incidencias = MovimientosIncidencias::where("id", $value->id)->where("incidencias_id", $data->id)->first();
                        }else
                            $movimientos_incidencias = new MovimientosIncidencias;

                        $movimientos_incidencias->servidor_id 	                  = env("SERVIDOR_ID");
                        $movimientos_incidencias->incidencias_id                  = $data->id;

                        $movimientos_incidencias->medico_reporta_id               = $value->medico_reporta_id;
                        $movimientos_incidencias->indicaciones                    = $value->indicaciones;
                        $movimientos_incidencias->reporte_medico                  = $value->reporte_medico;

                        $movimientos_incidencias->diagnostico_egreso              = $value->diagnostico_egreso;
                        $movimientos_incidencias->observacion_trabajo_social      = $value->observacion_trabajo_social;
                        $movimientos_incidencias->metodos_planificacion_id        = $value->metodos_planificacion_id;

                        $movimientos_incidencias->valoraciones_pacientes_id       = $value->valoraciones_pacientes_id;
                        $movimientos_incidencias->estados_pacientes_id            = $value->estados_pacientes_id;
                        $movimientos_incidencias->triage_colores_id               = $value->triage_colores_id;
                        $movimientos_incidencias->subcategorias_cie10_id          = $value->subcategorias_cie10_id;
                        $movimientos_incidencias->turnos_id                       = $value->turnos_id;

                        $movimientos_incidencias->save();

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
                foreach ($detalleReferencia as $key => $value) {
                    //validar que el valor no sea null
                    if($value != null){
                        //comprobar si el value es un array, si es convertirlo a object mas facil para manejar.
                        if(is_array($value))
                            $value = (object) $value;

                        //comprobar que el dato que se envio no exista o este borrado, si existe y esta borrado poner en activo nuevamente
                        if(property_exists($value, "id")) {
                            DB::update("update referencias set deleted_at = null where id = '$value->id' and incidencias_id = '$data->id' ");
                            //si existe actualizar
                            $referencia = Referencias::where("id", $value->id)->where("incidencias_id", $data->id)->first();
                        }else
                            $referencia = new Referencias;

                        $referencia->servidor_id 	                = env("SERVIDOR_ID");
                        $referencia->incidencias_id                 = $data->id;
                        $referencia->medico_refiere_id              = $value->medico_refiere_id;
                        $referencia->diagnostico                    = $value->diagnostico;
                        $referencia->resumen_clinico                = $value->resumen_clinico;
                        $referencia->clues_origen                   = $value->clues_origen;
                        $referencia->clues_destino                  = $value->clues_destino;
                        $referencia->img                            = $this->convertir_imagen($value->img, 'referencias', $data->id);
                        $referencia->esContrareferencia             = $value->esContrareferencia;

                        $referencia->save();
                    }
                }
            }


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
