<?php

namespace App\Http\Controllers\Administracion;

use App\Events\NuevoMensaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\User;
use App\Model\Messages;
class ChatController extends Controller
{
    public function ListaContactos(Request $request){
        if(!$request->ajax()) return redirect("/");
        try{
            $usuarios = DB::select("select usuarios.* ,
            (select count(msg.id) TotalMsg from messages as msg where msg.to = '".\Auth::user()->Usuario."' and `read` =0 and  `from` = usuarios.Usuario) as ExtensionTel,
            (select (msg.text)  from messages as msg where msg.to = '".\Auth::user()->Usuario."' and `read` =0 and  `from` = usuarios.Usuario ORDER BY id DESC limit 1) as UltMensaje
            from usuarios where Usuario <> '".\Auth::user()->Usuario."' and Inactivo = 0   ORDER BY ExtensionTel DESC ");
            //$usuarios = $usuarios->get();
            return [
                'contactos'=>$usuarios,
                'status'=>200
            ];
        }
        catch(Exception $e){
            return [
                'msg'=>"error al cargar la lista de contactos",
                'status'=>500
            ];
        }
    }

    public function ListaConversaciones(Request $request){
        if(!$request->ajax()) return redirect("/");
        $nIdContacto = $request->nIdContacto;
        $User = \Auth::user()->Usuario;
        //Marcamos la conversacion como leida al abrir el chat
        $UpdateRead = DB::select("update messages set `read`=1,updated_at = NOW()   where `from` = '".$nIdContacto."' and `to` ='".$User."' and `read` =0");
        $Mensajes = Messages::with('remitente','destinatario')->where("from", $User)->where("to",$nIdContacto)->orWhere("from", $nIdContacto)->where("to",$User)->get();
        return [
            'conversacion'=> $Mensajes
        ];
    }

    public function setRegistrarMensaje(Request $request)
    {
        try {
            DB::beginTransaction();
            $MsgNew = new Messages();
            $MsgNew->from = \Auth::user()->Usuario;
            $MsgNew->to = $request->to;
            $MsgNew->text = $request->text;
            $MsgNew->save();
            $MsgNew = Messages::with('remitente','destinatario')->where("id", $MsgNew->id)->get();
            broadcast(new NuevoMensaje($MsgNew[0]));
            DB::commit();
            return[
                'msg'=>$MsgNew,
                'status'=>200
            ];
        } catch (Exception $th) {
            DB::rollBack();
            return[
                'msg'=>'ocurrio un error al enviar el mensaje '.$th->getMessage(),
                'status'=>200
            ];
        }
    }
}
