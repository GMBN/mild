<?php

$tabela = isset($argv[2]) ? $argv[2] : null;

//verifica se e para processar apenas uma tabela
if ($tabela) {
    $name = $tabela;
    echo amarelo('Gerando model da tabela "' . $name . '"');
    $desc = (new Base\Table())->desc($name);
    if (count($desc) == 0) {
        vermelho('tabela "' . $name . '" nao existe no banco de dados');
        return ;
    }
    layoutPHP($name, $desc);
    return;
}

//processa todas as tabelas
$tables = (new Base\Table())->showAll();


foreach ($tables as $t) {
    $name = array_values($t)[0];
    echo amarelo('Gerando model da tabela "' . $name . '"');
    $desc = (new Base\Table())->desc($name);
    layoutPHP($name, $desc);
}

//print_r($desc);



function layoutPHP($tabela, $desc) {
    $php = '<?php ' . "\n\n";
    $php .= 'namespace Model ;' . "\n\n";
    //gera a classe
    $php .= ' class ' . toCamelCase($tabela) . ' extends \Base\DAO {' . "\n\n";

    $php .= '   protected $_table = "' . $tabela . '"; ' . "\n";

    //gera os atributos
    foreach ($desc as $d) {
        $field = $d['Field'];
        $php .= '   protected $' . $field . '; ' . "\n";
    }

    $php .= "\n\n";
    //gera os metodos
    foreach ($desc as $d) {
        $field = $d['Field'];
        $php .= '     function set' . toCamelCase($field) . '($' . $field . '){ ' . "\n";
        $php .= '           $this->' . $field . '=$' . $field . ";\n";
        $php .= '   }' . "\n\n";
    }

    $php .= ' }';

    $arquivo = toCamelCase($tabela);
    savePHP($arquivo, $php);
}

function toCamelCase($text) {
    $part = explode('_', $text);
    $uc = array_map(function($word) {
        return ucfirst($word);
    }, $part);
    $camel = implode('', $uc);
    return $camel;
}

function savePHP($name, $php) {
    $dir = ROOT . '/app/Model/' . $name . '.php';
    $f = file_put_contents($dir, $php);

    if ($f) {
        echo verde('    Arquivo ' . $name . '.php salvo com sucesso');
    } else {
        echo vermelho('    Falha ao salvar o arquivo ' . $name . '.php');
    }
}
