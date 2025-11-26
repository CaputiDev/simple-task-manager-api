<?php

require_once __DIR__ . '/../config.php';

use Database\Database;
use Error\ApiException;


//rodar via terminal:
//php src/database/setup.php
try {
    $pdo = Database::getConnection();

    echo "Conectado ao banco\n";

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("DROP TABLE IF EXISTS relatorios");
    $pdo->exec("DROP TABLE IF EXISTS usuarios");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    $sqlUsuarios = "
        CREATE TABLE usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY, -- Mudou de INTEGER AUTOINCREMENT
            nome VARCHAR(255) NOT NULL,        -- Mudou de TEXT
            email VARCHAR(100) NOT NULL UNIQUE,
            senha_hash VARCHAR(255) NOT NULL,
            ativo TINYINT(1) DEFAULT 1,        -- MySQL não tem BOOLEAN nativo, usa TINYINT
            cargo VARCHAR(50) DEFAULT 'user',
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ";

    $sqlRelatorios = "
        CREATE TABLE relatorios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            titulo VARCHAR(255) NOT NULL,
            cliente VARCHAR(255) NOT NULL,
            descricao TEXT,
            data_realizacao DATE NOT NULL,
            valor DECIMAL(10, 2) DEFAULT 0,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Recurso legal do MySQL: atualiza sozinho
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;
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
        ['nome' => 'Thiago', 'email' => 'thiago@proki.com', 'senha_hash' => $senhaPadrao, 'cargo' => 'user'],
        ['nome' => 'Miguel', 'email' => 'miguel@proki.com', 'senha_hash' => $senhaPadrao, 'cargo' => 'user'],
        ['nome' => 'Raul', 'email' => 'raul@proki.com', 'senha_hash' => $senhaPadrao, 'cargo' => 'user'],
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