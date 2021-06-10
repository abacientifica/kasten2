<?php

/*
 * El usuario autentificado
 * El id que hemos pasado
*/

Broadcast::channel('mensaje.{Usuario}', function ($user, $id) {
    return (string) $user->Usuario ===  (string) $id;
});

Broadcast::channel('escribiendo', function ($user) {
    return \Auth::check();
});

Broadcast::channel('logout.{Usuario}', function ($user,$id) {
     return (string) $user->Usuario ===  (string) $id;
});
/*Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});*/
