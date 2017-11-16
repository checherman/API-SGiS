<?php

namespace App\Http\Controllers\V1\Reportes;

use App\Http\Controllers\v1\ExportController;

use App\Models\Transacciones\EstadosFuerza;
use App\Models\Transacciones\Incidencias;
use \Validator,\Hash, \Response;
use Request;

class ReporteController extends ExportController {

    public function incidencias(){
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

        if(array_key_exists('edo_incidencia', $datos) && $datos['edo_incidencia'] != ""){
            $data = $data->where('estados_incidencias_id', $datos['edo_incidencia']);
        }

        //Movimientos
        if(array_key_exists('color_triage', $datos) && $datos['codigo_triage'] != ""){
            $data = $data->join('movimientos_incidencias', 'movimientos_incidencias.incidencias_id', '=', 'incidencias.id')
                         ->where('movimientos_incidencias.triage_colores_id', $datos['color_triage']);
        }

        if(array_key_exists('edo_paciente', $datos) && $datos['edo_paciente'] != ""){
            $data = $data->join('movimientos_incidencias', 'movimientos_incidencias.incidencias_id', '=', 'incidencias.id')
                         ->where('movimientos_incidencias.estados_pacientes_id', $datos['edo_paciente']);
        }

        if(array_key_exists('cie10', $datos) && $datos['cie10'] != ""){
            $data = $data->join('movimientos_incidencias', 'movimientos_incidencias.incidencias_id', '=', 'incidencias.id')
                         ->where('movimientos_incidencias.subcategorias_cie10_id', $datos['cie10']);
        }

        if(array_key_exists('turno', $datos) && $datos['turno'] != ""){
            $data = $data->join('movimientos_incidencias', 'movimientos_incidencias.incidencias_id', '=', 'incidencias.id')
                         ->where('movimientos_incidencias.turnos_id', $datos['turno']);
        }

        //Referencias
        if(array_key_exists('referencia_origen', $datos) && $datos['referencia_origen'] != ""){
            $data = $data->join('referencias', 'referencias.incidencias_id', '=', 'incidencias.id')
                         ->where('referencias.clues_origen', $datos['referencia_origen']);
        }

        if(array_key_exists('referencia_destino', $datos) && $datos['referencia_destino'] != ""){
            $data = $data->join('referencias', 'referencias.incidencias_id', '=', 'incidencias.id')
                         ->where('referencias.clues_destino', $datos['referencia_destino']);
        }

        //Altas
        if(array_key_exists('tipo_alta', $datos) && $datos['tipo_alta'] != ""){
            $data = $data->join('altas_incidencias', 'altas_incidencias.incidencias_id', '=', 'incidencias.id')
                         ->where('altas_incidencias.tipos_altas_id', $datos['tipo_alta']);
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

        $data = EstadosFuerza::select('estados_fuerza.*')
                             ->with("clues", "turnos", "respuesta_estados_fuerza");

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

        if(!$data){
            return Response::json(array("status" => 404, "messages" => "No hay resultados"), 404);
        }
        else{
            return Response::json(array("status" => 200, "messages" => "Operación realizada con exito", "data" => $data, "total" => count($total)), 200);

        }
    }

    public function excel()
    {
        $datos = Request::all();

        Excel::create('Reporte_Incidencias'.date('d-m-Y H-i-s'), function($excel)use($parametros){

            $excel->sheet('Incidencias', function($sheet)use($parametros)
            {
                $items = $this->getItemsInventario($parametros);

                $claves       = "";
                $seleccionar  = "";
                $tipo_insumos = "";
                $clave        = "";

                if($parametros['buscar_en'] == "TODAS_LAS_CLAVES")
                {
                    $claves = "TODAS LAS CLAVES";
                }else{
                    $claves = "MIS CLAVES";
                }
                if($parametros['seleccionar'] == "TODO")
                {+
                    $seleccionar = "TODOS INSUMOS";
                }
                if($parametros['seleccionar'] == "EXISTENTE")
                {
                    $seleccionar = "INSUMOS EXISTENTES";
                }
                if($parametros['seleccionar'] == "NO_EXISTENTE")
                {
                    $seleccionar = "INSUMOS AGOTADOS";
                }
                if($parametros['tipo'] == "TODO")
                {
                    $tipo_insumos = "TODOS";
                }
                if($parametros['tipo'] == "CAUSES")
                {
                    $tipo_insumos = "MED. CAUSES";
                }
                if($parametros['tipo'] == "NO_CAUSES")
                {
                    $tipo_insumos = "MED. NO CAUSES";
                }
                if($parametros['tipo'] == "CONTROLADO")
                {
                    $tipo_insumos = "MED. CONTROLADO";
                }


                $sheet->row(2, array('','INVENTARIO DE ALMACÉN '.$parametros['almacen'].' EN CLUES '.$parametros['clues'].' AL '.date('d-m-Y H:i:s'),'','','','','','','','','',''));
                $sheet->row(2, function($row) {
                    $row->setBackground('#DDDDDD');
                    $row->setFontWeight('bold');
                    $row->setFontSize(14);
                });

                $sheet->row(4, array('','BUSQUEDA EN : '.$claves.' | SELECCIONAR : '.$seleccionar.' | TIPO INSUMOS : '.$tipo_insumos.' | CLAVE : '.$parametros['clave_insumo']));

                $sheet->row(4, function($row) {
                    $row->setBackground('#DDDDDD');
                    $row->setFontWeight('bold');
                    $row->setFontSize(12);
                });

                $sheet->row(6, array('Clave','Descripción', 'C.P.D','C.P.S','C.P.M','Existencia','Existencia Unidosis'));
                $sheet->row(6, function($row) {
                    $row->setBackground('#DDDDDD');
                    $row->setFontWeight('bold');
                    $row->setFontSize(12);
                });



                $sheet->cells("A6:M6", function($cells) {
                    $cells->setAlignment('center');
                });

                $sheet->setSize('A2', 25, 18);
                $sheet->setSize('B2', 70, 18);
                $sheet->setSize('F2', 20, 18);
                $sheet->setSize('G2', 30, 18);

                $sheet->setSize('A4', 25, 18);
                $sheet->setSize('B4', 70, 18);
                $sheet->setSize('F4', 20, 18);
                $sheet->setSize('G4', 30, 18);

                $sheet->setSize('A6', 25, 18);
                $sheet->setSize('B6', 70, 18);
                $sheet->setSize('F6', 20, 18);
                $sheet->setSize('G6', 30, 18);

                foreach($items as $item)
                {
                    //$sheet->setColumnFormat(array('J' => '0.00', 'K' => '0.00'));

                    $sheet->appendRow(array(

                        $item->clave_insumo_medico,
                        $item->descripcion,
                        "--",
                        "--",
                        "--",
                        $item->existencia,
                        $item->existencia_unidosis
                    ));


                } // FIN FOREACH ITEMS

            });
        })->export('xls');
    }

}