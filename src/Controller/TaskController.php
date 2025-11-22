<?php

namespace Controller;

use Service\TaskService;
use Http\Request;
use Http\Response;
use Error\APIException;
use Utils\Jwt;

class TaskController
{
    private TaskService $service;

    public function __construct()
    {
        $this->service = new TaskService();
    }

    //o método principal que o index.php chama
    public function processRequest(Request $request)
    {
        $method = $request->getMethod();
        $id = $request->getId();
        $body = $request->getBody();
        $query = $request->getQuery();

        //autenticacao JWT
        $usuarioLogado = $this->autenticarUsuario();

        //pega os dados
        $usuarioId = $usuarioLogado['id'];
        $usuarioRole = $usuarioLogado['role'];

        // router interno do Controller
        switch ($method) {
            case 'GET':
                if ($id) {
                    $dado = $this->service->buscarPorId($id);
                    if($dado['usuario_id'] !== $usuarioId && $usuarioRole !== 'admin')throw new APIException("Acesso negado. Você não é o dono deste relatório.", 403);
                    Response::send($dado);
                } else {
                    $lista = $this->service->listar($usuarioId, $usuarioRole, $query);
                    Response::send($lista);
                }
                break;

            case 'POST':
                $body['usuario_id'] = $usuarioId;
                
                $novo = $this->service->criar($body);
                Response::send($novo, 201);
                break;

            case 'PUT':
                if (!$id) throw new APIException("ID obrigatório", 400);
                
                $atualizado = $this->service->atualizar($id, $body, $usuarioId, $usuarioRole);
                Response::send($atualizado);
                break;

            case 'DELETE':
                if (!$id) throw new APIException("ID obrigatório", 400);
                
                $this->service->deletar($id, $usuarioId, $usuarioRole);
                Response::send(null, 204);
                break;

            default:
                throw new APIException("Método não permitido", 405);
        }
    }
        private function autenticarUsuario(): array
    {
        $headers = getallheaders();
        
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (!$authHeader) {
            throw new APIException("Token de autenticação não fornecido.", 401);
        }

        // Remove o prefixo "Bearer "
        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            throw new APIException("Formato do token inválido. Use: Bearer <token>", 401);
        }

        $token = $matches[1];

        try {
            $payload = Jwt::validate($token);
            
            // Retorna id e cargo do usuário
            return [
                'id' => $payload['sub'],
                'role' => $payload['role'] ?? 'user'
            ];

        } catch (\Exception $e) {
            throw new APIException("Acesso negado: " . $e->getMessage(), 401);
        }
    }
}