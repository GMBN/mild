<?php

namespace Base;

class Route {

    private $routes;
    private $server;
    private $request_uri;
    private $request_method;
    private $base;

    function __construct() {
        //atribui as variaveis do server
        $this->server = $_SERVER;

        //obtem a uri acessada
        $this->request_uri = $this->server['REQUEST_URI'];

        //obtem o verbo da requisicao
        $this->request_method = $this->server['REQUEST_METHOD'];

        //obtem apenas a url com barras
        $this->base = explode('?', $this->request_uri)[0];
    }

    function post($url, $controller) {
        $this->routes[$url . '@POST'] = $controller;
    }

    function get($url, $controller) {
        $this->routes[$url . '@GET'] = $controller;
    }

    function all($url, $controller) {
        $this->routes[$url . '@ALL'] = $controller;
    }

    function ajax($url, $controller) {
        $this->routes[$url . '@AJAX'] = $controller;
    }

    function notFount() {
        echo "404";
    }

    function execute() {
        //obtem apenas todas as rotas definidas
        $routes = array_keys($this->routes);

        $request = explode('/', $this->request_uri);

        foreach ($routes as $r) {

            $par = [];
            //separa o verbo da uri
            $base = explode('@', $r);
            //uri esperada
            $uri = $base[0];
            //verbo esperada
            $verbo = $base[1];

            //quebra a uri esperada nas barras
            $folders = explode('/', $uri);

            //recebe as uri estatica com os parametros dinamicos substituidos
            $staticUrl = [];

            //percorre a uri esperada para substituir os parametros dinamicos

            foreach ($folders as $seq => $f) {
                //verifica se e um parametro dinamico dentro da url definida
                $isPar = (substr($f, 0, 1) == ':') ? true : false;

                if ($isPar) {
                    $staticUrl[$seq] = isset($request[$seq]) ? $request[$seq] : null;
                    $par[] = $staticUrl[$seq];
                } else {
                    $staticUrl[$seq] = $f;
                }
            }

            $urlParser = implode('/', $staticUrl);

            //compara a quantidade de pasta das url esperada com a requisicao 
            if (count($folders) == count($request)) {

                //compara se a uri esperada e igual a da requisicao
                if ($urlParser == $this->request_uri) {

                    //compara se o verbo e permitido
                    if ($verbo == 'ALL' || $verbo != '@AJAX' || $verbo == $this->request_method) {
                        $class = explode(':', $this->routes[$r]);
                        $controller = $class[0];
                        $action = $class[1];

                        //executa o controller e renderiza a view
                        return (new View($controller, $action, $par, $verbo))->render();
                    } else {
                        return Log::erro('Verbo "' . $this->request_method . '" nao permitido');
                    }
                }
            }
        }

        $this->notFount();
    }

}
