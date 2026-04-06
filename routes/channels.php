<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('diagrama.{politicaId}', function ($user, $politicaId) {
    // Todos los usuarios logueados pueden entrar para simplificar. 
    // Puedes restringir por colaborador luego.
    return [
         'id' => $user->id,
         'name' => $user->name,
         'email' => $user->email,
    ];
});
