<?php

require_once __DIR__ . '/../config.php';

use Database\Database;
use Error\ApiException;


//rodar via terminal:
//php src/database/setup.php
try {
    $pdo = Database::getConnection();

    echo "Conectado ao banco\n";

    // Limpeza (DEBUG)
    $pdo->exec("DROP TABLE IF EXISTS relatorios;");
    $pdo->exec("DROP TABLE IF EXISTS usuarios;");

    // Criação das Tabelas
    $sqlUsuarios = "
        CREATE TABLE usuarios (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nome TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            senha_hash TEXT NOT NULL,
            ativo INTEGER DEFAULT 1,
            criado_em TEXT DEFAULT CURRENT_TIMESTAMP,
            cargo TEXT DEFAULT 'user'
        );
    ";

    $sqlRelatorios = "
        CREATE TABLE relatorios (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            usuario_id INTEGER NOT NULL,
            titulo TEXT NOT NULL,
            cliente TEXT NOT NULL,
            descricao TEXT,
            data_realizacao TEXT NOT NULL,
            valor REAL DEFAULT 0,
            data_criacao TEXT DEFAULT CURRENT_TIMESTAMP,
            data_atualizacao TEXT DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
        );
    ";

    // Executa a criação
    $pdo->exec($sqlUsuarios);
    $pdo->exec($sqlRelatorios);
    echo " Tabelas criadas com sucesso!\n";

    $pdo->beginTransaction();

    // Criando Usuários padrão e o admin
    // senha debug para testes
    $senhaPadrao = password_hash('senha123', PASSWORD_DEFAULT);
    $senhaAdmin =  password_hash('admin', PASSWORD_DEFAULT);
    
    $usuariosSeed = [
        ['nome' => 'admin', 'email' => 'admin@admin.com', 'senha_hash' => $senhaAdmin, 'cargo' => 'admin'],
        ['nome' => 'Thiago', 'email' => 'thiago@email.com', 'senha_hash' => $senhaPadrao, 'cargo' => 'user'],
        ['nome' => 'Miguel', 'email' => 'miguel@email.com', 'senha_hash' => $senhaPadrao, 'cargo' => 'user'],
        ['nome' => 'Raul', 'email' => 'raul@email.com', 'senha_hash' => $senhaPadrao, 'cargo' => 'user'],
    ];

    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash, cargo) VALUES (:nome, :email, :senha_hash, :cargo)");
    
    foreach ($usuariosSeed as $user) {
        $stmt->execute($user);
    }

    echo " Usuários de teste inseridos!\n";

    // Criando Relatórios Falsos (DEBUG)
    $relatoriosSeed = [
        [
            'usuario_id' => 2,
            'titulo' => 'Manutenção de PC',
            'cliente' => 'Empresa X',
            'data_realizacao' => date('Y-m-d'),
            'valor' => 150.00
        ],
        [
            'usuario_id' => 3,
            'titulo' => 'Instalação de Rede',
            'cliente' => 'Escola Y',
            'data_realizacao' => date('Y-m-d', strtotime('-1 day')),
            'valor' => 500.00
        ],
        [
            'usuario_id' => 4,
            'titulo' => 'Serviço genérico',
            'cliente' => 'Ifsul Câmpus Charqueadas',
            'data_realizacao' => date('Y-m-d', strtotime('-3 day')),
            'valor' => 20.00
        ]
    ];

    $stmtRel = $pdo->prepare("INSERT INTO relatorios (usuario_id, titulo, cliente, data_realizacao, valor) VALUES (:usuario_id, :titulo, :cliente, :data_realizacao, :valor)");

    foreach ($relatoriosSeed as $r) {
        $stmtRel->execute($r);
    }

    echo "Relatórios de teste inseridos!\n";

    // Confirma
    $pdo->commit();
    echo "Banco de dados configurado e pronto para uso!\n";

} catch (Exception $e) {
    // Se der erro, desfaz tudo o que foi feito na transação
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Erro no setup: " . $e->getMessage() . "\n";
}