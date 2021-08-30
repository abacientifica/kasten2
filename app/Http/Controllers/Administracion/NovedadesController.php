<?php

namespace App\Http\Controllers\Administracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class NovedadesController extends Controller
{
    public function getNovedadesItem(Request $request){
        if(!$request->ajax()) return  redirect('/');
        $Item = $request->IdItem;
        $Novedades = DB::select("select * from novedades where Solucionada =0 and IdItem=".$Item);
        return[
            'novedades'=>$Novedades
        ];
    }
}
