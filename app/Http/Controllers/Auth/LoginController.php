<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $rpta = Auth::attempt(['Usuario'=>$request->cUsuario,'password'=>$request->cContrasena,'Inactivo'=>0]);
        if($rpta){
            return [
                'authUser'=> \Auth::user(),
                'code'=>200
            ];
        }
        else{
            return [
                'code'=>401,
                withErrors(['usuario'=>trans('auth.failed')])->withInput(['usuario'=>$request->usuario])
                
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
