<?php

namespace App\Http\Controllers\Cotizaciones;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\CotizacionesTipo;

class TiposController extends Controller
{
    public function getTiposCotizaciones(Request $request){
        $Tipos = CotizacionesTipo::with('tiposubtipo.subtipos')->get();
        return [
            'tipos_cot'=>$Tipos
        ];
    }
}
