<?php

namespace App\Http\Controllers\V1\Dashboard;

use App\Models\Transacciones\Incidencias;
use \Validator,\Hash, \Response;
use Request;

class DashboardController
{
    public function index()
    {
        $datos = array();
        $cluesH = Request::header('clues');


        //Triage 1.-Verde 2.-Amarillo 3.-Rojo
        $totalVerde = Incidencias::select('incidencias.*')->with("pacientes.personas", "pacientes.acompaniantes.personas")
            ->with("movimientos_incidencias", "referencias", "altas_incidencias", "estados_incidencias")->join('movimientos_incidencias', 'movimientos_incidencias.incidencias_id', '=', 'incidencias.id')
            ->where('movimientos_incidencias.triage_colores_id', 1)
            ->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
            ->where('incidencia_clue.clues',$cluesH)->count();

        $totalAmarillo = Incidencias::select('incidencias.*')->with("pacientes.personas", "pacientes.acompaniantes.personas")
            ->with("movimientos_incidencias", "referencias", "altas_incidencias", "estados_incidencias")->join('movimientos_incidencias', 'movimientos_incidencias.incidencias_id', '=', 'incidencias.id')
            ->where('movimientos_incidencias.triage_colores_id', 2)
            ->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
            ->where('incidencia_clue.clues',$cluesH)->count();

        $totalRojo = Incidencias::select('incidencias.*')->with("pacientes.personas", "pacientes.acompaniantes.personas")
            ->with("movimientos_incidencias", "referencias", "altas_incidencias", "estados_incidencias")->join('movimientos_incidencias', 'movimientos_incidencias.incidencias_id', '=', 'incidencias.id')
            ->where('movimientos_incidencias.triage_colores_id', 3)
            ->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
            ->where('incidencia_clue.clues',$cluesH)->count();

        $triage = array();
        array_push($triage, ['nombre' => 'Verde', 'total' => $totalVerde]);
        array_push($triage, ['nombre' => 'Amarillo', 'total' => $totalAmarillo]);
        array_push($triage, ['nombre' => 'Rojo', 'total' => $totalRojo]);







        array_push($datos, ['triage' => $triage]);

        if (!$datos) {
            return Response::json(array("status" => 404, "messages" => "No hay resultados"), 404);
        } else {
            return Response::json(array("status" => 200, "messages" => "Operación realizada con exito", "data" => $datos), 200);
        }
    }
}

