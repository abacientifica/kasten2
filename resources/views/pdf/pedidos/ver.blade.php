<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$movimiento[0]->documento->Nombre."-".$movimiento[0]->NroDocumento}}</title>
    <link type="text/css" href="{{ base_path() }}/resources/vendor/css/estylepdf.css" rel="stylesheet" />
    <style>
        .img-logo{
            width:150px;
        }
        .label-titulo{
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="content container-fluid">
    <img src="{{ base_path() }}/public/img/kasten/logoaba.png" class="img-logo">
    <p align="center">{{$movimiento[0]->documento->Nombre.' Nro. '.$movimiento[0]->NroDocumento}}</p>
    <hr>
    <p>{{$movimiento[0]->tercero->NombreCorto}}</p>
    <hr>
    <table width="100%" cellspacing="0" cellspacing="1" border="1" align="center" style="font-size: 10px;">
            <tr>
                <td>
                    <table width="100%" cellspacing="0" cellspacing="1" align="center">
                        <tr>
                            <td class="label-titulo">Id Movimiento:</td>
                            <td>{{ $movimiento[0]->IdMovimiento }}</td>
                        </tr>
                        <tr>
                            <td class="label-titulo">Nro. Documento:</td>
                            <td>{{ $movimiento[0]->NroDocumento }}</td>
                        </tr>
                        <tr>
                            <td class="label-titulo">Fecha Creación:</td>
                            <td>{{ date_format(date_create($movimiento[0]->FhAutoriza),'Y-m-d') }}</td>
                        </tr>
                        <tr>
                            <td class="label-titulo">Fecha Entrega:</td>
                            <td>{{ date_format(date_create($movimiento[0]->Fecha2),'Y-m-d') }}</td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table width="100%" cellspacing="0" cellspacing="1" align="center">
                        <tr>
                            <td class="label-titulo">Condición Entrega:</td>
                            <td>{{$movimiento[0]->condentrega->NmCondicionEntrega}}</td>
                        </tr>
                        <tr>
                            <td class="label-titulo">Estado:</td>
                            <td>{{$movimiento[0]->Estado}}</td>
                        </tr>
                        <tr>
                            <td class="label-titulo">Nro. Orden:</td>
                            <td>{{$movimiento[0]->Soporte}}</td>
                        </tr>
                        <tr>
                            <td class="label-titulo">Dirección:</td>
                            <td>{{$movimiento[0]->direccion->NmDireccion}}</td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table width="100%" cellspacing="0" cellspacing="1" align="center">
                        <tr>
                            <td class="label-titulo">Asesor:</td>
                            <td>{{ $movimiento[0]->asesor->Nombre }}</td>
                        </tr>
                        <tr>
                            <td class="label-titulo">Forma Pago:</td>
                            <td>{{ $movimiento[0]->fpago->FormaPago }}</td>
                        </tr>
                        <tr>
                            <td class="label-titulo">Comentarios:</td>
                            <td>{{ $movimiento[0]->Comentarios }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    <hr>
            <table class="table table-sm table-bordered table-striped" style="font-size: 10px;">
            <thead>
                <tr style="background:#333366 !important;color:white">
                    <th class="texto-centrado">Cod. Aba</th>
                    <th class="texto-centrado">Descripción</th>
                    <th class="texto-centrado">Referencia</th>
                    <th class="texto-centrado">Marca</th>
                    <th class="texto-centrado">UMV</th>
                    <th class="texto-centrado">Precio</th>
                    <th class="texto-centrado">Cant</th>
                    <th class="texto-centrado">Iva</th>
                    <th class="texto-centrado">Subtotal</th>
                </tr>
            </thead>
            
            <tbody>
                @if(count($movimientos_det)>0)
                    @foreach($movimientos_det as $det)
                    <tr style="font-size: 8px;">
                        <td>{{$det->Id_Item}}</td>
                        <td>{{$det->item->Descripcion}}</td>
                        <td>{{$det->item->listacostosdet->RefFabricante}}</td>
                        <td>{{$det->item->listacostosdet->marca->NmMarca}}</td>
                        <td>{{$det->item->UMM}}</td>
                        <td>{{number_format($det->Precio,2,',','.')}}</td>
                        <td>{{$det->Cantidad}}</td>
                        <td>{{$det->PorIva}}</td>
                        <td>{{number_format($det->SubTotal,2,',','.')}}</td>
                    </tr>
                    @endforeach
                    <tr style="background-color: #9090e6 !important;font-size: 8px;">
                        <td colspan="8" align="right"><strong>SubTotal:</strong></td>
                        <td class="texto-derecha">${{number_format($movimiento[0]->SubTotal,2,',','.')}}</td>
                    </tr>

                    <tr style="background-color: #9090e6 !important;font-size: 8px;">
                        <td colspan="8" align="right"><strong>Total Iva:</strong></td>
                        <td class="texto-derecha">${{number_format($movimiento[0]->VrIva,2,',','.')}}</td>
                    </tr>

                    <tr style="background-color: #9090e6 !important;font-size: 8px;">
                        <td colspan="8" align="right"><strong>Total:</strong></td>
                        <td class="texto-derecha">${{number_format($movimiento[0]->Total,2,',','.')}}</td>
                    </tr>
                @endif
            </tbody>
        </table>
</div>
<style>
.page-break {
    page-break-after: always;
}
</style>
</body>
</html>