<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Catalogos\Jurisdicciones;
use App\Models\Catalogos\Clues;
use App\Models\Catalogos\SubCategoriasCie10;
use App\Models\Sistema\Permiso;

use App\Models\Sistema\SisUsuario;
use App\Models\Transacciones\Acompaniantes;
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

        $data =  Clues::with('jurisdicciones')->where(function($query) use ($parametros) {
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

        $data =  SisUsuario::where(function($query) use ($parametros) {
            $query->where('nombre','LIKE',"%".$parametros['term']."%")
                ->orWhere('username','LIKE',"%".$parametros['term']."%");
        });

        $data = $data->get();
        return $this->respuestaVerTodo($data);
    }

}