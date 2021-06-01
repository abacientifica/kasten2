<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use App\Events\UserLogin;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $this->validatelogin($request);
        $errors = new MessageBag;
        $rpta = Auth::attempt(['Usuario'=>$request->cUsuario,'password'=>$request->cContrasena,'Inactivo'=>0]);
        if($rpta){
            event(new UserLogin(\Auth::user()));
            return [
                'authUser'=> \Auth::user(),
                'status'=>200
            ];
        }
        else{
            return [
                'status'=>401,
                'msg'=>"Las credenciales no funcionaron, intenta de nuevo"
            ];
        }   

        //return back()->withErrors(['usuario'=>trans('auth.failed')])->withInput(['usuario'=>$request->usuario]);
    }

    protected function validatelogin($request)
    {
        $this->validate($request,[
            'cUsuario'=>'required|string',
            'cContrasena'=>'required|string',
        ]);
    }

    public function logout(Request $request){
        \Auth::logout();
        return ['code'=>204];
    }
}
