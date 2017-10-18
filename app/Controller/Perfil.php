<?php

namespace Controller;

class Perfil {

    function view($slug) {
        
        $config = new \Model\Config();
        $rs = $config->findAll();
        print_r($rs);
        echo 'Bem Vindo ' . $slug;
        return ['testeNome' => strtoupper($slug)];
    }

}
