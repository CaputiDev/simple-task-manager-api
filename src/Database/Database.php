<?php

namespace Database;

use PDO;
use PDOException;
use Error\APIException;

class Database
{
    // Configurações do MySQL
    private static string $host = 'localhost'; 
    private static string $db_name = 'proki';
    private static string $username = 'proki'; 
    private static string $password = 'senha123';
    
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            try {
                // A String de Conexão (DSN) muda para MySQL
                $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=utf8";
                
                self::$connection = new PDO($dsn, self::$username, self::$password);

				// exceções de erro
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                
                // charset 
                self::$connection->exec("set names utf8");

            } catch (PDOException $e) {
                // Dica: Mostra o erro exato de conexão (IP errado, senha errada, etc)
                throw new APIException("Erro de Conexão MySQL: " . $e->getMessage(), 500); 
            }
        }

        return self::$connection;
    }
}