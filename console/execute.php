<?php

//inclui as definicoes globais
require __DIR__ . '/../define.php';

//verifica se exibira os erros
if (DEBUG) {
    ini_set('display_errors', 1);
}

require ROOT . '/autoload.php';

//verifica se o acesso e via terminal
if (php_sapi_name() !== 'cli') {
    return \Base\Log::erro("Acesso Permitido apenas via terminal");
}


$servico = isset($argv[1]) ? $argv[1] : false;

//se o servico existe
if ($servico) {
    // executa o servico
    $file = __DIR__ . '/src/' . $servico . '.php';
    if (file_exists($file)) {
        require_once $file;
    } else {
        echo vermelho(' O servico "' . $servico . '" nao existe');
    }
} else {
    $diretorio = dir(__DIR__ . '/src/');
    $ignore = ['.', '..'];

    echo amarelo("Servicos disponivels:");

    while ($arquivo = $diretorio->read()) {
        //ignora os diretorios
        if (in_array($arquivo, $ignore)) {
            continue;
        }

        $name = str_replace('.php', '', $arquivo);
        //exibe os servicos disponiveis em verde
        echo  verde($name) ;
    }
}


function verde($text){
    return "\033[32m ".$text. "\n";
}
function vermelho($text){
    return "\033[31m ".$text. "\n";
}
function amarelo($text){
    return "\033[33m ".$text. "\n";
}