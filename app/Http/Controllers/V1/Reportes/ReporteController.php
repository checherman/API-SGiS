<?php

namespace App\Http\Controllers\V1\Reportes;

use App\Http\Controllers\v1\ExportController;

use App\Models\Catalogos\Clues;
use App\Models\Catalogos\NivelesCones;
use App\Models\Transacciones\EstadosFuerza;
use App\Models\Transacciones\Incidencias;
use Illuminate\Support\Facades\DB;
use \Validator,\Hash, \Response;
use Request;

class ReporteController extends ExportController {

    public function incidenciasIngreso(){
        $datos = Request::all();

        $data = Incidencias::select('incidencias.*')->with("pacientes.personas", "pacientes.acompaniantes.personas")
                           ->with("movimientos_incidencias", "referencias", "altas_incidencias", "estados_incidencias");


        if(array_key_exists('fecha_inicio', $datos) && $datos['fecha_inicio'] != ""  && array_key_exists('fecha_fin', $datos) && $datos['fecha_fin'] != ""){
            $data = $data->whereBetween('incidencias.created_at', array($datos['fecha_inicio'], $datos['fecha_fin']));
        }

        if(array_key_exists('clues', $datos) && $datos['clues'] != ""){
            $data = $data->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
                         ->where('incidencia_clue.clues', $datos['clues']);
        }

        //Movimientos
        if(array_key_exists('color_triage', $datos) && $datos['color_triage'] != ""){
            $data = $data->join('movimientos_incidencias as A', 'A.incidencias_id', '=', 'incidencias.id')
                         ->where('A.triage_colores_id', $datos['color_triage'])
                         ->whereNull('A.medico_reporta_id')
                         ->whereNull('A.reporte_medico')
                         ->whereNull('A.indicaciones');

        }

        if(array_key_exists('edo_paciente', $datos) && $datos['edo_paciente'] != ""){
            $data = $data->join('movimientos_incidencias as B', 'B.incidencias_id', '=', 'incidencias.id')
                         ->where('B.estados_pacientes_id', $datos['edo_paciente'])
                         ->whereNull('B.medico_reporta_id')
                         ->whereNull('B.reporte_medico')
                         ->whereNull('B.indicaciones');
        }

        if(array_key_exists('cie10', $datos) && $datos['cie10'] != ""){
            $data = $data->join('movimientos_incidencias as C', 'movimientos_incidencias.incidencias_id', '=', 'incidencias.id')
                         ->where('C.subcategorias_cie10_id', $datos['cie10'])
                         ->whereNull('C.medico_reporta_id')
                         ->whereNull('C.reporte_medico')
                         ->whereNull('C.indicaciones');
        }

        if(array_key_exists('turno', $datos) && $datos['turno'] != ""){
            $data = $data->join('movimientos_incidencias as D', 'D.incidencias_id', '=', 'incidencias.id')
                         ->where('D.turnos_id', $datos['turno'])
                         ->whereNull('D.medico_reporta_id')
                         ->whereNull('D.reporte_medico')
                         ->whereNull('D.indicaciones');
        }


        $total = $data->get();
        $data = $data->get();

        if(!$data){
            return Response::json(array("status" => 404, "messages" => "No hay resultados"), 404);
        }
        else{
            return Response::json(array("status" => 200, "messages" => "Operación realizada con exito", "data" => $data, "total" => count($total)), 200);

        }
    }

    public function incidenciasReferencia(){
        $datos = Request::all();

        $data = Incidencias::select('incidencias.*')->with("pacientes.personas", "pacientes.acompaniantes.personas")
            ->with("movimientos_incidencias", "referencias", "altas_incidencias", "estados_incidencias");


        if(array_key_exists('fecha_inicio', $datos) && $datos['fecha_inicio'] != ""  && array_key_exists('fecha_fin', $datos) && $datos['fecha_fin'] != ""){
            $data = $data->whereBetween('incidencias.created_at', array($datos['fecha_inicio'], $datos['fecha_fin']));
        }

        //if(array_key_exists('clues', $datos) && $datos['clues'] != ""){
            //$data = $data->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
                //->where('incidencia_clue.clues', $datos['clues']);
        //}


        //Referencias
        if(array_key_exists('referencia_origen', $datos) && $datos['referencia_origen'] != ""){
            $data = $data->join('referencias as A', 'A.incidencias_id', '=', 'incidencias.id')
                ->where('A.clues_origen', $datos['referencia_origen']);
        }

        if(array_key_exists('referencia_destino', $datos) && $datos['referencia_destino'] != ""){
            $data = $data->join('referencias as B', 'B.incidencias_id', '=', 'incidencias.id')
                ->where('B.clues_destino', $datos['referencia_destino']);
        }


        $total = $data->get();
        $data = $data->get();

        if(!$data){
            return Response::json(array("status" => 404, "messages" => "No hay resultados"), 404);
        }
        else{
            return Response::json(array("status" => 200, "messages" => "Operación realizada con exito", "data" => $data, "total" => count($total)), 200);

        }
    }

    public function incidenciasAlta(){
        $datos = Request::all();

        $data = Incidencias::select('incidencias.*')->with("pacientes.personas", "pacientes.acompaniantes.personas")
            ->with("movimientos_incidencias", "referencias", "altas_incidencias", "estados_incidencias");


        if(array_key_exists('fecha_inicio', $datos) && $datos['fecha_inicio'] != ""  && array_key_exists('fecha_fin', $datos) && $datos['fecha_fin'] != ""){
            $data = $data->whereBetween('incidencias.created_at', array($datos['fecha_inicio'], $datos['fecha_fin']));
        }

        if(array_key_exists('clues', $datos) && $datos['clues'] != ""){
            $data = $data->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
                ->where('incidencia_clue.clues', $datos['clues']);
        }


        //Altas
        if(array_key_exists('tipo_alta', $datos) && $datos['tipo_alta'] != ""){
            $data = $data->join('altas_incidencias as A', 'A.incidencias_id', '=', 'incidencias.id')
                ->where('A.tipos_altas_id', $datos['tipo_alta']);
        }

        if(array_key_exists('metodos_planificacion', $datos) && $datos['metodos_planificacion'] != ""){
            $data = $data->join('altas_incidencias as B', 'B.incidencias_id', '=', 'incidencias.id')
                ->where('B.metodos_planificacion_id', $datos['metodos_planificacion']);
        }

        if(array_key_exists('turno', $datos) && $datos['turno'] != ""){
            $data = $data->join('altas_incidencias as C', 'C.incidencias_id', '=', 'incidencias.id')
                ->where('C.turnos_id', $datos['turno']);
        }


        $total = $data->get();
        $data = $data->get();

        if(!$data){
            return Response::json(array("status" => 404, "messages" => "No hay resultados"), 404);
        }
        else{
            return Response::json(array("status" => 200, "messages" => "Operación realizada con exito", "data" => $data, "total" => count($total)), 200);

        }
    }

    public function estadoFuerza(){
        $datos = Request::all();




        $nivelCONE = Clues::select("nivel_cone_id")->where('clues', $datos['clues'])->first();
        $nivelesCones = NivelesCones::find($nivelCONE->nivel_cone_id);


        $ahora = date("Y-m-d h:i:s");
        $data = collect();
        $data->put('clues', "");
        $data->put('sis_usuarios_id', "");
        $data->put('turnos_id', "");
        $data->put('created_at', $ahora);
        $data->put('usuario', "");

        $carteraServicios = $nivelesCones->carteraServicio()->with("items")->get();

        $data->put('cartera_servicios', $carteraServicios);

        //foreach ($data as $key => $value) {
            //$respuestas_estados_fuerza = DB::table('respuestas_estados_fuerza')->where('estados_fuerza_id', $value->id)->first();
            //$value->respuestas_estados_fuerza = $respuestas_estados_fuerza->respuesta;
        //}



        foreach ($data['cartera_servicios'] as $key => $value) {
            foreach ($value->items as $keyI => $item) {
                $respuestas_estados_fuerza = DB::table('respuestas_estados_fuerza')->where('items_id', $item->id)->get();
                foreach($respuestas_estados_fuerza as $respuestaEstado => $respuesta_item){
                    //dd($respuesta_item->respuesta);
                    $item->respuesta = $respuesta_item->respuesta;
                }
                //dd($respuestas_estados_fuerza["respuesta"]);

                //$item->respuesta = "";
            }
        }



        $total = $data;
        //$data = $data->get();


        if(!$data){
            return Response::json(array("status" => 404, "messages" => "No hay resultados"), 404);
        }
        else{
            return Response::json(array("status" => 200, "messages" => "Operación realizada con exito", "data" => $data, "total" => count($total)), 200);

        }
    }

    public function estadoFuerzaLeve(){

        //--------------------------------------
        $datos = Request::all();

        $data = EstadosFuerza::select('estados_fuerza.*')
            ->with("clues", "turnos", "sis_usuarios","respuesta_estados_fuerza");

        if(array_key_exists('fecha_inicio', $datos) && $datos['fecha_inicio'] != ""  && array_key_exists('fecha_fin', $datos) && $datos['fecha_fin'] != ""){
            $data = $data->whereBetween('estados_fuerza.created_at', array($datos['fecha_inicio'], $datos['fecha_fin']));
        }

        if(array_key_exists('clues', $datos) && $datos['clues'] != ""){
            $data = $data->where('clues', $datos['clues']);
        }

        if(array_key_exists('turnos', $datos) && $datos['turnos'] != ""){
            $data = $data->where('turnos_id', $datos['turnos']);
        }

        $total = $data->get();
        $data = $data->get();

        //foreach ($data as $key => $value) {
        //$respuestas_estados_fuerza = DB::table('respuestas_estados_fuerza')->where('estados_fuerza_id', $value->id)->first();
        //$value->respuestas_estados_fuerza = $respuestas_estados_fuerza->respuesta;
        //}


        if(!$data){
            return Response::json(array("status" => 404, "messages" => "No hay resultados"), 404);
        }
        else{
            return Response::json(array("status" => 200, "messages" => "Operación realizada con exito", "data" => $data, "total" => count($total)), 200);

        }
    }


}