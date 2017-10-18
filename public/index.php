<?php

//inclui as definicoes globais
require __DIR__ . '/../define.php';

//verifica se exibira os erros
if (DEBUG) {
    ini_set('display_errors', 1);
}

require ROOT . '/autoload.php';


require ROOT . '/app/route.php';
