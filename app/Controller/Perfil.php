<?php

namespace Controller;

class Perfil {

    function view($slug) {
//        echo 'Bem Vindo ' . $slug;
        return ['testeNome' => strtoupper($slug)];
    }

}
