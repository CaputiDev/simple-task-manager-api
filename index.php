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
    
    // Rota: '/api/usuarios'
    case 'usuarios':
        $controller = new UserController();
        $controller->processRequest($request);
        break;

    // Rota: '/api/login'
    case 'login':
        $controller = new LoginController();
        $controller->processRequest($request);
        break;

    // Rota: '/api/relatorios'
    case 'relatorios':
        $controller = new TaskController();
        $controller->processRequest($request);
        break;

    // Rota raiz '/'
    case null:
        $endpoints = [
            "POST /api/usuarios" => "Criar Conta",
            "POST /api/login" => "Autenticar",
            "POST /api/relatorios" => "Criar relatório",
            "GET /api/relatorios/:id" => "Ver relatorio especifico",
            "PUT /api/relatorios/:id" => "Atualizar relatorio",
            "DELETE /api/relatorios/:id" => "Excluir relatorio",
            "GET(admin) /api/relatorios" => "Listar todos relatórios",
            "GET(admin) /api/usuarios" => "Listar todos usuarios",
        ];
        Response::send(["autores" => "Thiago Caputi, Raul Lize Teixeira, Miguel Leonardo Lewandowiski ", "api" => "api", "versao" => "1.0.0", "endpoints" => $endpoints]);
        break;

    // Qualquer outra rota
    default:
        throw new ApiException("Not Found", 404);
}