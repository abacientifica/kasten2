<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdministracionReportesController extends Controller
{
    public function index(Request $request){
        $Columnas = DB::select("SHOW COLUMNS FROM temp_ventas_general");
        $Datos = DB::select("SELECT * FROM temp_ventas_general WHERE IdUsuario ='ivan'");
        return[
            'columnas'=>$Columnas,
            'datos'=>$Datos,
            'status'=>200
        ];
    }
}
