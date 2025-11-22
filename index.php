<?php

require_once 'src/config.php';

use Controller\UserController;
use Controller\LoginController;
use Controller\TaskController;
use Http\Request;
use Http\Response;
use Error\APIException;

// prepara a requisição 
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER["REQUEST_METHOD"];
$body = file_get_contents("php://input");

// Cria o objeto Request
$request = new Request($uri, $method, $body);

// router
switch ($request->getResource()) {
    
    // Rota: '/proki/usuarios'
    case 'usuarios':
        $controller = new UserController();
        $controller->processRequest($request);
        break;

    // Rota: '/proki/login'
    case 'login':
        $controller = new LoginController();
        $controller->processRequest($request);
        break;

    // Rota: '/proki/relatorios'
    case 'relatorios':
        $controller = new TaskController();
        $controller->processRequest($request);
        break;

    // Rota raiz '/'
    case null:
        $endpoints = [
            "POST /proki/usuarios" => "Criar Conta",
            "POST /proki/login" => "Autenticar",
            "GET /proki/relatorios" => "Listar todos relatórios",
            "POST /proki/relatorios" => "Criar relatório",
            "GET /proki/relatorios/:id" => "Ver relatorio especifico",
            "PUT /proki/relatorios/:id" => "Atualizar relatorio",
            "DELETE /proki/relatorios/:id" => "Excluir relatorio"
        ];
        Response::send(["autores" => "Thiago Caputi, Raul Lize Teixeira, Miguel Leonardo Lewandowiski ", "api" => "Proki-Mini", "versao" => "1.0.0", "endpoints" => $endpoints]);
        break;

    // Qualquer outra rota
    default:
        throw new ApiException("Not Found", 404);
}