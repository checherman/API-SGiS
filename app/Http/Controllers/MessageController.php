<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Events\MessagePublished;
use App\Models\Sistema\Chat as ChatModel;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Events\Dispatcher;

use App\Models\Sistema\SisUsuario;
use DB, Session, Response, Input;

class MessageController extends Controller
{
    private $messages;

    public function __construct(ChatModel $messages)
    {
        $this->messages = $messages;
    }

    /**
     * Display last 20 messages
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
        $usuario = Session::get('usuario'); 
        var_dump($usuario);
        $usuarios = DB::select(" select id, nombre, username, avatar, email, DATEDIFF((select max(created_at) from chats where sis_usuarios_de = sis_usuarios.id), now()) as online from sis_usuarios where id not in(".$usuario->id.") and deleted_at is null");  
        $chats = $this->messages->where("sis_usuarios_de", $usuario->id)->orWhere("sis_usuarios_para", $usuario->id)->orderBy('id', 'asc')
        ->groupBy('sis_usuarios_de')
        ->groupBy('sis_usuarios_para')
        ->get([DB::raw('MAX(id) as id'), 'sis_usuarios_de', 'sis_usuarios_para']);
        
        foreach ($chats as $key => $value) {
            $existe = false;
            foreach ($chats as $k1 => $v1) {
                if($v1->sis_usuarios_de == $value->sis_usuarios_para && $v1->sis_usuarios_para == $value->sis_usuarios_de){
                    $existe  = true;
                    unset($chats[$key]);
                }
            }
            if(!$existe){
                $value->para = SisUsuario::find($value->sis_usuarios_para);
                $value->de = SisUsuario::find($value->sis_usuarios_de);  
                $msj = $this->messages->select("mensaje")->where("id", $value->id)->first();
                if($msj)
                    $value->mensaje =  $msj->mensaje;
                else
                    $value->mensaje = ""; 
            }      
                
        }
        return Response::json(["chats" => $chats, "token" => csrf_token(), "usuarios" => $usuarios]);
    }

    /**
     * Store a newly created message
     *
     * @param  IlluminateHttpRequest  $request
     * @return IlluminateHttpResponse
     */
    public function store(Request $request, Dispatcher $event)
    {
        $usuario = Session::get('usuario');

        $datos = Input::json();
        $message = new ChatModel;

        $message->sis_usuarios_de = $usuario->id;
        $message->sis_usuarios_para = $datos->get("para");
        $message->mensaje = $datos->get("message");
        $message->autor = $datos->get("username");
        $message->save();

        $message->para = SisUsuario::find($message->sis_usuarios_para);
        $message->de = SisUsuario::find($message->sis_usuarios_de);

        $chats = $this->messages->where("sis_usuarios_de", $usuario->id)->orWhere("sis_usuarios_para", $usuario->id)->orderBy('id', 'asc')
        ->groupBy('sis_usuarios_de')
        ->groupBy('sis_usuarios_para')
        ->get([DB::raw('MAX(id) as id'), 'sis_usuarios_de', 'sis_usuarios_para']);
        
        foreach ($chats as $key => $value) {
            $existe = false;
            foreach ($chats as $k1 => $v1) {
                if($v1->sis_usuarios_de == $value->sis_usuarios_para && $v1->sis_usuarios_para == $value->sis_usuarios_de){
                    $existe  = true;
                    unset($chats[$key]);
                }
            }
            if(!$existe){
                $value->para = SisUsuario::find($value->sis_usuarios_para);
                $value->de = SisUsuario::find($value->sis_usuarios_de);  
                $msj = $this->messages->select("mensaje")->where("id", $value->id)->first();
                if($msj)
                    $value->mensaje =  $msj->mensaje;
                else
                    $value->mensaje = ""; 
            }      
                
        }

        $event->fire(new MessagePublished(["messages" => $message, "token" => csrf_token(), "chats" => $chats ]));
        
        return Response::json(["messages" => $message, "token" => csrf_token(), "chats" => $chats ], 201);
    }

    public function show($id)
    {  
        $usuario = Session::get('usuario');
        $data = $this->messages->select("id")->where("sis_usuarios_de", $usuario->id)->orWhere("sis_usuarios_para", $usuario->id)->get();
        
        $chats = $this->messages->whereIn("id", $data);
        $chats = $chats->whereNested(function($query) use ($id){                 
            $query->where("sis_usuarios_de", $id)->orWhere("sis_usuarios_para", $id); 
        });
        $chats = $chats->orderBy('id', 'desc')->take(20)->get()->reverse();
        foreach ($chats as $key => $value) {
            $value->para = SisUsuario::find($value->sis_usuarios_para);
            $value->de = SisUsuario::find($value->sis_usuarios_de);
        } 
        return Response::json(["messages" => $chats, "token" => csrf_token()]);
    }

    public function update($id)
    {  
        $usuario = Session::get('usuario');
        $chats = $this->messages->where("sis_usuarios_de", $usuario->id)->orWhere("sis_usuarios_para", $usuario->id)->orderBy('id', 'asc')
        ->groupBy('sis_usuarios_de')
        ->groupBy('sis_usuarios_para')
        ->get([DB::raw('MAX(id) as id'), 'sis_usuarios_de', 'sis_usuarios_para']);
        
        foreach ($chats as $key => $value) {
            $existe = false;
            foreach ($chats as $k1 => $v1) {
                if($v1->sis_usuarios_de == $value->sis_usuarios_para && $v1->sis_usuarios_para == $value->sis_usuarios_de){
                    $existe  = true;
                    unset($chats[$key]);
                }
            }
            if(!$existe){
                $value->para = SisUsuario::find($value->sis_usuarios_para);
                $value->de = SisUsuario::find($value->sis_usuarios_de);  
                $msj = $this->messages->select("mensaje")->where("id", $value->id)->first();
                if($msj)
                    $value->mensaje =  $msj->mensaje;
                else
                    $value->mensaje = ""; 
            }      
                
        }
        return Response::json(["chats" => $chats, "token" => csrf_token()]);
    }

    public function destroy($id)
    {
        $success = false;
        DB::beginTransaction();
        try {
            $usuario = Session::get('usuario');
            $data = $this->messages->select("id")->where("sis_usuarios_de", $usuario->id)->orWhere("sis_usuarios_para", $usuario->id)->get();
        
            $chats = $this->messages->whereIn("id", $data);
            $chats = $chats->whereNested(function($query) use ($id){                 
                $query->where("sis_usuarios_de", $id)->orWhere("sis_usuarios_para", $id); 
            });
            $chats = $chats->orderBy('id', 'desc')->get()->reverse();

            foreach ($chats as $key => $value) {
                $data = ChatModel::find($value->id);
                $data->delete();
            }
            $success = true;
        } 
        catch (Exception $e){
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
}
