<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Catalogos\Jurisdicciones;
use App\Models\Catalogos\Clues;
use App\Models\Catalogos\NivelesCones;
use App\Models\Catalogos\SubCategoriasCie10;
use App\Models\Sistema\Permiso;

use App\Models\Sistema\SisUsuario;
use App\Models\Transacciones\Acompaniantes;
use App\Models\Transacciones\EstadosFuerza;
use App\Models\Transacciones\Incidencias;
use App\Models\Transacciones\Personas;
use Illuminate\Support\Facades\Input;
use \Validator,\Hash, \Response;
use DB;



class AutoCompleteController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function grupo_permiso()
    {
        $parametros = Input::only('term');

        $data =  Permiso::where(function($query) use ($parametros) {
            $query->where('grupo','LIKE',"%".$parametros['term']."%");
        });

        $variable = $data->distinct()->select(DB::raw("grupo as nombre"))->get();
        $data = [];
        foreach ($variable as $key => $value) {
            $data[] = $value->nombre;
        }

        return $this->respuestaVerTodo($data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function clues()
    {
        $parametros = Input::only('term');

        $data =  Clues::with('jurisdicciones')
            ->where("activo", 1)
            ->where(function($query) use ($parametros) {
                $query->where('clues','LIKE',"%".$parametros['term']."%")
                      ->orWhere('nombre','LIKE',"%".$parametros['term']."%");
            });

        $data = $data->get();
        return $this->respuestaVerTodo($data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function clues_fuerza()
    {
        $parametros = Input::only('term');

        $data =  Clues::with('jurisdicciones')
            ->where("activo", 1)
            ->where(function($query) use ($parametros) {
                $query->where('clues','LIKE',"%".$parametros['term']."%")
                    ->orWhere('nombre','LIKE',"%".$parametros['term']."%");
            });


        $data = $data->get();
        foreach ($data as $keyData => $valueData) {
            $estadofuerza = EstadosFuerza::select('estados_fuerza.*')->where("clues", $valueData->clues)
                ->orderBy('created_at', 'desc')->first();

            if($valueData->nivel_cone_id && $estadofuerza){

                $valueData->estado_fuerza = $estadofuerza;

                $nivelCONE = Clues::select("nivel_cone_id")->where('clues', $valueData->clues)->first();
                $nivelesCones = NivelesCones::find($nivelCONE->nivel_cone_id);
                $carteraServicios = $nivelesCones->carteraServicio()->with("items")->get();


                $valueData->estado_fuerza->cartera_servicios = $carteraServicios;
                foreach($valueData->estado_fuerza->cartera_servicios as $keyCartera => $valueCartera){
                        foreach ($valueCartera->items as $keyI => $item) {

                            $itemG = DB::table('respuestas_estados_fuerza')
                                ->where('estados_fuerza_id', $valueData->estado_fuerza->id)
                                ->where('cartera_servicios_id', $valueCartera->id)
                                ->where('items_id', $item->id)->first();

                            $item->respuesta = $itemG->respuesta;
                        }

                    }

            }else{
                $valueData->estado_fuerza = "";
            }

        }

        return $this->respuestaVerTodo($data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function jurisdiccion_clues()
    {
        $parametros = Input::only('term');

        $data =  Clues::with('jurisdicciones')->where(function($query) use ($parametros) {
            $query->where('jurisdicciones_id',$parametros['term']."%");
        });

        $data = $data->get();

        return $this->respuestaVerTodo($data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function personas()
    {
        $parametros = Input::only('term');

        $acompaniantes = Acompaniantes::select('personas_id')->get();

        $data =  Personas::with('municipios','localidades','derechohabientes', 'estados_embarazos')->whereNotIn('id', $acompaniantes)
            ->where(function($query) use ($parametros) {
            $query->where('id','LIKE',"%".$parametros['term']."%");
        });

        $data = $data->get();
        return $this->respuestaVerTodo($data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function subcategoriascie10()
    {
        $parametros = Input::only('term');

        $data =  SubCategoriasCie10::where(function($query) use ($parametros) {
            $query->where('codigo','LIKE',"%".$parametros['term']."%")
                  ->orWhere('nombre','LIKE',"%".$parametros['term']."%");
        });

        $data = $data->get();
        return $this->respuestaVerTodo($data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function usuarios()
    {
        $parametros = Input::only('term');

        $data =  SisUsuario::with("SisUsuariosGrupos", "SisUsuariosContactos")->where(function($query) use ($parametros) {
            $query->where('nombre','LIKE',"%".$parametros['term']."%")
                ->orWhere('username','LIKE',"%".$parametros['term']."%");
        });

        $data = $data->get();
        return $this->respuestaVerTodo($data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function topCie10()
    {
        $data =  Incidencias::select('S.id','S.nombre', 'S.codigo', DB::raw('count(M.subcategorias_cie10_id) as total'))
            ->join('movimientos_incidencias as M', 'M.incidencias_id', '=', 'incidencias.id')
            ->join('subcategorias_cie10 as S', 'M.subcategorias_cie10_id', '=', 'S.id')
            ->groupBy('M.subcategorias_cie10_id')
            ->orderBy('total', 'DESC')
            ->limit(10);

        $data = $data->get();
        return $this->respuestaVerTodo($data);
    }

}