<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

use \Response;

trait ApiRespuestas
{
    private function respuestaExitosa($data, $code)
    {
        return Response::json($data, $code);
    }

    protected function respuestaError($message, $code)
    {
        return Response::json(['error' => $message, 'code' => $code], $code);
    }

    protected function respuestaVerTodo($collection, $code = 200)
    {
        return $this->respuestaExitosa(['data' => $collection], $code);
    }

    protected function respuestaVerUno(Model $instance, $code = 200)
    {
        return $this->respuestaExitosa(['data' => $instance], $code);
    }
}