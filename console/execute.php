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
        echo "\033[31m O servico  " . $servico . " nao existe \n";
    }
} else {
    $diretorio = dir(__DIR__ . '/src/');
    $ignore = ['.', '..'];

    echo "\033[33m Servicos disponivels: \n";

    while ($arquivo = $diretorio->read()) {
        //ignora os diretorios
        if (in_array($arquivo, $ignore)) {
            continue;
        }

        $name = str_replace('.php', '', $arquivo);
        //exibe os servicos disponiveis em verde
        echo "\033[32m " . $name . "\n";
    }
}
