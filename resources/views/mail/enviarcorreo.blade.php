@extends('layouts.mail')

@section('contenido')

<div class="contenido">
    <p class="mensaje" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787e; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
        {{$mensaje}}
    </p>
</div>

@endsection