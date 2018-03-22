<?php

namespace App\Http\Controllers\V1\Dashboard;

use App\Models\Transacciones\Incidencias;
use \Validator,\Hash, \Response;
use Request;
use Carbon\Carbon;

class DashboardController
{
    public function index()
    {
        $datos = array();
        $cluesH = Request::header('clues');

        $data = Incidencias::select('incidencias.*');

        $data = $data->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
            ->where('incidencia_clue.clues', $cluesH);

        $data = $data->join('movimientos_incidencias as M', 'M.incidencias_id', '=', 'incidencias.id')
            ->whereNull('M.medico_reporta_id')
            ->whereNull('M.reporte_medico')
            ->whereNull('M.indicaciones');


        $data = $data->get();

        $totalVerde = 0;
        $totalAmarillo = 0;
        $totalRojo = 0;

        foreach ($data as $d => $value){
            if($data[$d]->movimientos_incidencias[sizeof($data[$d]->movimientos_incidencias)-1]["triage_colores_id"] == 1){
                $totalVerde = $totalVerde + 1;
            }
            if($data[$d]->movimientos_incidencias[sizeof($data[$d]->movimientos_incidencias)-1]["triage_colores_id"] == 2){
                $totalAmarillo = $totalAmarillo + 1;
            }
            if($data[$d]->movimientos_incidencias[sizeof($data[$d]->movimientos_incidencias)-1]["triage_colores_id"] == 3){
                $totalRojo = $totalRojo + 1;
            }
        }
        $totalTriage = $totalVerde + $totalAmarillo + $totalRojo;

        $triage = array();
        array_push($triage, ['nombre' => 'Verde', 'total' => $totalVerde]);
        array_push($triage, ['nombre' => 'Amarillo', 'total' => $totalAmarillo]);
        array_push($triage, ['nombre' => 'Rojo', 'total' => $totalRojo]);
        array_push($datos, ['totalTriage' => $totalTriage]);
        array_push($datos, ['triage' => $triage]);

        //Grafica altas
        $totalMejoria = Incidencias::select('incidencias.*')
            ->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
            ->where('incidencia_clue.clues', $cluesH)
            ->join('altas_incidencias as A', 'A.incidencias_id', '=', 'incidencias.id')
            ->where('A.tipos_altas_id', 1)->count();

        $totalVoluntaria = Incidencias::select('incidencias.*')
            ->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
            ->where('incidencia_clue.clues', $cluesH)
            ->join('altas_incidencias as B', 'B.incidencias_id', '=', 'incidencias.id')
            ->where('B.tipos_altas_id', 2)->count();

        $totalDefunsion = Incidencias::select('incidencias.*')
            ->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
            ->where('incidencia_clue.clues', $cluesH)
            ->join('altas_incidencias as C', 'C.incidencias_id', '=', 'incidencias.id')
            ->where('C.tipos_altas_id', 3)->count();

        $totalAltas = $totalMejoria + $totalVoluntaria + $totalDefunsion;

        $altas = array();
        array_push($altas, ['nombre' => 'Mejoria', 'total' => $totalMejoria]);
        array_push($altas, ['nombre' => 'Voluntaria', 'total' => $totalVoluntaria]);
        array_push($altas, ['nombre' => 'Defunsion', 'total' => $totalDefunsion]);
        array_push($datos, ['totalAltas' => $totalAltas]);
        array_push($datos, ['altas' => $altas]);

        //Promedio hospitalizacion
        $promedioHospitalizacion = Carbon::now();
        array_push($datos, ['fdfd' => $promedioHospitalizacion]);

        if (!$datos) {
            return Response::json(array("status" => 404, "messages" => "No hay resultados"), 404);
        } else {
            return Response::json(array("status" => 200, "messages" => "Operación realizada con exito", "data" => $datos), 200);
        }
    }
}

