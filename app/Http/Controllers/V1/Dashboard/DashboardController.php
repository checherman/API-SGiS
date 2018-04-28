<?php

namespace App\Http\Controllers\V1\Dashboard;

use App\Models\Transacciones\Incidencias;
use App\Models\Transacciones\MovimientosIncidencias;
use Illuminate\Support\Facades\DB;
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

        //Top diagnostico cie10 (ingresos)
        $dataCie10 = Incidencias::select('S.nombre', 'S.codigo', DB::raw('count(M.subcategorias_cie10_id) as total'))
            ->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
            ->where('incidencia_clue.clues', $cluesH)
            ->join('movimientos_incidencias as M', 'M.incidencias_id', '=', 'incidencias.id')
            ->join('subcategorias_cie10 as S', 'M.subcategorias_cie10_id', '=', 'S.id')
            ->whereNull('M.medico_reporta_id')
            ->whereNull('M.reporte_medico')
            ->whereNull('M.indicaciones')
            ->groupBy('M.subcategorias_cie10_id')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get();
        array_push($datos, ['topCie10' => $dataCie10]);

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

        //Clasificacion de ingresos
        $clasificacionIngreso = array();
        $totalConReferencia = Incidencias::select('incidencias.*')
            ->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
            ->where('incidencia_clue.clues', $cluesH)
            ->join('referencias as C', 'C.incidencias_id', '=', 'incidencias.id')
            ->where('C.esIngreso', 1)->count();
        $procentajeConReferencia = ($totalConReferencia * 100) / $totalTriage;

        $totalSinReferencia = $totalTriage - $totalConReferencia;
        $procentajeSinReferencia = ($totalSinReferencia * 100) / $totalTriage;

        array_push($clasificacionIngreso, ['nombre' => 'Ingresos Con Referencia', 'total' => $totalConReferencia, 'porcentaje' => round($procentajeConReferencia,2)]);
        array_push($clasificacionIngreso, ['nombre' => 'Ingresos Sin Referencia', 'total' => $totalSinReferencia, 'porcentaje' => round($procentajeSinReferencia,2)]);
        array_push($datos, ['clasificacionIngreso' => $clasificacionIngreso]);


        //Clasificacion pacientes por edad
        $clasificacionPorEdad = array();
        $total12a15 = Incidencias::select('incidencias.*')
            ->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
            ->where('incidencia_clue.clues', $cluesH)
            ->join('incidencia_paciente', 'incidencia_paciente.incidencias_id','=','incidencias.id')
            ->join('pacientes', 'pacientes.id','=','incidencia_paciente.pacientes_id')
            ->join('personas', 'personas.id','=','pacientes.personas_id')
            ->whereBetween('fecha_nacimiento', ['2003-01-01', '2006-01-01'])->count();
        $procentaje12a15 = ($total12a15 * 100) / $totalTriage;

        $total15a18 = Incidencias::select('incidencias.*')
            ->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
            ->where('incidencia_clue.clues', $cluesH)
            ->join('incidencia_paciente', 'incidencia_paciente.incidencias_id','=','incidencias.id')
            ->join('pacientes', 'pacientes.id','=','incidencia_paciente.pacientes_id')
            ->join('personas', 'personas.id','=','pacientes.personas_id')
            ->whereBetween('fecha_nacimiento', ['2000-01-01', '2003-01-01'])->count();
        $procentaje15a18 = ($total15a18 * 100) / $totalTriage;

        $total18a20 = Incidencias::select('incidencias.*')
            ->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
            ->where('incidencia_clue.clues', $cluesH)
            ->join('incidencia_paciente', 'incidencia_paciente.incidencias_id','=','incidencias.id')
            ->join('pacientes', 'pacientes.id','=','incidencia_paciente.pacientes_id')
            ->join('personas', 'personas.id','=','pacientes.personas_id')
            ->whereBetween('fecha_nacimiento', ['1998-01-01', '2000-01-01'])->count();
        $procentaje18a20 = ($total18a20 * 100) / $totalTriage;

        $total20a22 = Incidencias::select('incidencias.*')
            ->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
            ->where('incidencia_clue.clues', $cluesH)
            ->join('incidencia_paciente', 'incidencia_paciente.incidencias_id','=','incidencias.id')
            ->join('pacientes', 'pacientes.id','=','incidencia_paciente.pacientes_id')
            ->join('personas', 'personas.id','=','pacientes.personas_id')
            ->whereBetween('fecha_nacimiento', ['1996-01-01', '1998-01-01'])->count();
        $procentaje20a22 = ($total20a22 * 100) / $totalTriage;

        $total22a24 = Incidencias::select('incidencias.*')
            ->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
            ->where('incidencia_clue.clues', $cluesH)
            ->join('incidencia_paciente', 'incidencia_paciente.incidencias_id','=','incidencias.id')
            ->join('pacientes', 'pacientes.id','=','incidencia_paciente.pacientes_id')
            ->join('personas', 'personas.id','=','pacientes.personas_id')
            ->whereBetween('fecha_nacimiento', ['1994-01-01', '1996-01-01'])->count();
        $procentaje22a24 = ($total22a24 * 100) / $totalTriage;

        $total24a26 = Incidencias::select('incidencias.*')
            ->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
            ->where('incidencia_clue.clues', $cluesH)
            ->join('incidencia_paciente', 'incidencia_paciente.incidencias_id','=','incidencias.id')
            ->join('pacientes', 'pacientes.id','=','incidencia_paciente.pacientes_id')
            ->join('personas', 'personas.id','=','pacientes.personas_id')
            ->whereBetween('fecha_nacimiento', ['1992-01-01', '1994-01-01'])->count();
        $procentaje24a26= ($total24a26 * 100) / $totalTriage;

        $total26a28 = Incidencias::select('incidencias.*')
            ->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
            ->where('incidencia_clue.clues', $cluesH)
            ->join('incidencia_paciente', 'incidencia_paciente.incidencias_id','=','incidencias.id')
            ->join('pacientes', 'pacientes.id','=','incidencia_paciente.pacientes_id')
            ->join('personas', 'personas.id','=','pacientes.personas_id')
            ->whereBetween('fecha_nacimiento', ['1990-01-01', '1992-01-01'])->count();
        $procentaje26a28 = ($total26a28 * 100) / $totalTriage;

        $total28a30 = Incidencias::select('incidencias.*')
            ->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
            ->where('incidencia_clue.clues', $cluesH)
            ->join('incidencia_paciente', 'incidencia_paciente.incidencias_id','=','incidencias.id')
            ->join('pacientes', 'pacientes.id','=','incidencia_paciente.pacientes_id')
            ->join('personas', 'personas.id','=','pacientes.personas_id')
            ->whereBetween('fecha_nacimiento', ['1988-01-01', '1990-01-01'])->count();
        $procentaje28a30 = ($total28a30 * 100) / $totalTriage;

        $totalMayorA30 = Incidencias::select('incidencias.*')
            ->join('incidencia_clue', 'incidencia_clue.incidencias_id', '=', 'incidencias.id')
            ->where('incidencia_clue.clues', $cluesH)
            ->join('incidencia_paciente', 'incidencia_paciente.incidencias_id','=','incidencias.id')
            ->join('pacientes', 'pacientes.id','=','incidencia_paciente.pacientes_id')
            ->join('personas', 'personas.id','=','pacientes.personas_id')
            ->where('fecha_nacimiento', '<','1988-01-01')->count();
        $procentajeMayorA30 = ($totalMayorA30 * 100) / $totalTriage;

        array_push($clasificacionPorEdad, ['nombre' => 'De 12 años a 15 años', 'total' => $total12a15, 'porcentaje' => round($procentaje12a15,2)]);
        array_push($clasificacionPorEdad, ['nombre' => 'De 15 años a 18 años', 'total' => $total15a18, 'porcentaje' => round($procentaje15a18,2)]);
        array_push($clasificacionPorEdad, ['nombre' => 'De 18 años a 20 años', 'total' => $total18a20, 'porcentaje' => round($procentaje18a20,2)]);
        array_push($clasificacionPorEdad, ['nombre' => 'De 20 años a 22 años', 'total' => $total20a22, 'porcentaje' => round($procentaje20a22,2)]);
        array_push($clasificacionPorEdad, ['nombre' => 'De 22 años a 24 años', 'total' => $total22a24, 'porcentaje' => round($procentaje22a24,2)]);
        array_push($clasificacionPorEdad, ['nombre' => 'De 24 años a 26 años', 'total' => $total24a26, 'porcentaje' => round($procentaje24a26,2)]);
        array_push($clasificacionPorEdad, ['nombre' => 'De 26 años a 28 años', 'total' => $total26a28, 'porcentaje' => round($procentaje26a28,2)]);
        array_push($clasificacionPorEdad, ['nombre' => 'De 28 años a 30 años', 'total' => $total28a30, 'porcentaje' => round($procentaje28a30,2)]);
        array_push($clasificacionPorEdad, ['nombre' => 'Mayores a 30 años', 'total' => $totalMayorA30, 'porcentaje' => round($procentajeMayorA30,2)]);
        array_push($datos, ['clasificacionPorEdad' => $clasificacionPorEdad]);

        //Porcentajes
        //foreach ($data as $d => $value){
            //$datosI = $data[$d]->movimientos_incidencias[sizeof($data[$d]->movimientos_incidencias)-1]['id'];
            //var_dump($datosI);

            //$data = MovimientosIncidencias::select('movimientos_incidencias.created_at', 'altas_incidencias.created_at')
                //->join('altas_incidencias', 'altas_incidencias.incidencias_id', '=', 'movimientos_incidencias.incidencias_id')
                //->where('movimientos_incidencias.id', $datosI);

        //}

        //$tiempoPromedioHospitalizacion = $data;
        //array_push($datos, ['tiempoPromedioHospitalizacion' => $tiempoPromedioHospitalizacion]);

        $procentajeExitoAtencion = ($totalMejoria * 100) / $totalAltas;
        array_push($datos, ['procentajeExitoAtencion' => round($procentajeExitoAtencion,2)]);

        //Metas Obstetricas
        $embarazos = array();
        $esperadoEmbarazos = 20;
        array_push($embarazos, ['esperadoEmbarazos' => $esperadoEmbarazos, 'atendidosEmbarazos' => $total12a15, 'porcentajeEmbarazos' => round($procentaje12a15,2)]);
        array_push($datos, ['embarazos' => $embarazos]);

        $partos = array();
        $esperadoPartos = 20;
        array_push($partos, ['esperadoPartos' => $esperadoPartos, 'atendidosPartos' => $total12a15, 'porcentajePartos' => round($procentaje12a15,2)]);
        array_push($datos, ['partos' => $partos]);

        $puerperio = array();
        $esperadoPuerperio = 20;
        array_push($puerperio, ['programadasPuerperio' => $esperadoPuerperio, 'realizadasPuerperio' => $total12a15, 'porcentajePuerperio' => round($procentaje12a15,2)]);
        array_push($datos, ['puerperio' => $puerperio]);

        if (!$datos) {
            return Response::json(array("status" => 404, "messages" => "No hay resultados"), 404);
        } else {
            return Response::json(array("status" => 200, "messages" => "Operación realizada con exito", "data" => $datos), 200);
        }
    }
}

