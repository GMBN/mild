<?php

namespace Base;

class View {

    private $content;
    private $controller;
    private $action;
    private $par;
    private $verbo;

    function __construct($controller, $action, $par, $verbo) {
        $this->controller = $controller;
        $this->action = $action;
        $this->par = $par;
        $this->verbo = $verbo;
    }

    function render() {

        $class = '\\Controller\\' . $this->controller;

        //verifica se existe o controller
        if (class_exists($class)) {

            //verifica se existe a action
            if (method_exists($class, $this->action)) {

                $dados = call_user_func_array([$class, $this->action], $this->par);

                //executa a view
                $this->view($dados);
            } else {
                return Log::erro('Action (metodo) "' . $this->action . '" nao encontrado no controller "' . $this->controller . '"');
            }
        } else {
            return Log::erro('Controller "' . $this->controller . '" nao encontrado');
        }
    }

    function view($dados) {

        $name = strtolower($this->controller);
        $viewFile = ROOT . '/app/View/' . $name . '/' . $this->action . '.php';

        //verifica se existe o arquivo da view
        if (!file_exists($viewFile)) {
            return Log::erro('View "' . $name . '/' . $this->action . '.php" nao encontrado');
        }

        //extrai as variaveis retornadas do controller

        if (is_array($dados)) {
            extract($dados);
        }

        //inicia a captura de conteudo
        ob_start();

        //inclui o arquivo da view
        require $viewFile;
        
        //obtem o conteudo e atribui a variavel content
        $this->content = ob_get_clean();
        
        //verifica se não é ajax e inclui o arquivo de layout
        if($this->verbo !='AJAX'){
            require_once ROOT.'/app/View/layout.php';
        }else{
            //exibe o conteudo
            echo $this->content;
        }
    }

}
