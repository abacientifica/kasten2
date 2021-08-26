@extends('layouts.mail')

@section('contenido')

<div class="contenido">
    <p>Este es un nuevo mensaje enviado desde ABA Cientifica :</p>
    <br><br>
    <p class="mensaje" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787e; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
        {!!$mensaje!!}
    </p>
    <br><hr>
    <p>Este correo es enviado automaticamente desde el sistema, por favor absténgase de responderlo, cualquier inquietud comuniquese con sistemas@aba.com.co o auxsistemas@aba.com.co</p>
</div>

@endsection