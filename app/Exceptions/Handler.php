<?php

namespace App\Exceptions;

use App\Traits\ApiRespuestas;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    use ApiRespuestas;
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        //Evitar la redireccion cuando falla la validacion
        if($exception instanceof ValidationException)
        {
            return $this->convertValidationExceptionToResponse($exception, $request);
        }

        //Modelo no encontrado (404)
        if($exception instanceof ModelNotFoundException)
        {
            $modelo = strtolower(class_basename($exception->getModel()));
            return $this->respuestaError("No existe ninguna instancia de {$modelo} con el id especificado", 404);
        }

        //No esta auntenticado
        if ($exception instanceof AuthenticationException)
        {
            return $this->unauthenticated($request, $exception);
        }

        //Validacion de la Autorizacion para realizar una accion
        if ($exception instanceof AuthorizationException)
        {
            return $this->respuestaError('No posee permisos para ejecutar esta accion', 403);
        }

        //Para acceder a una ruta que no existe
        if ($exception instanceof NotFoundHttpException)
        {
            return $this->respuestaError('No se encontro la URL especificada', 404);
        }

        //Metodo no permitido, no es el correcto
        if ($exception instanceof MethodNotAllowedHttpException)
        {
            return $this->respuestaError('El metodo especificado en la peticion no es valido', 404);
        }

        //Diferentes Excepciones HTTP
        if ($exception instanceof HttpException)
        {
            return $this->respuestaError($exception->getMessage(), $exception->getStatusCode());
        }

        //Al eliminar recursos anidados a otros
        if ($exception instanceof QueryException)
        {
            $codigo = $exception->errorInfo[1];

            if($codigo == 1451){
                return $this->respuestaError('No se puede eliminar el recurso porque esta relacionado con algun otro.', 409);
            }
        }

        if(config('app.debug')){
            return parent::render($request, $exception);
        }

        //Error de API
        return $this->respuestaError('Falla inesperada. Intente luego (API)', 500);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }

    /**
     * Create a response object from the given validation exception.
     *
     * @param  \Illuminate\Validation\ValidationException  $e
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->getMessages();

        return $this->errorResponse($errors, 422);

    }
}
