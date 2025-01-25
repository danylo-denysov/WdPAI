<?php

namespace App;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    private function __construct() {}

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
//            $host = 'localhost';
            $host = 'db';
            $port = '5432';
            $dbname = 'mydatabase';
            $user = 'myuser';
            $password = 'mypassword';

            $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";

            try {
                self::$connection = new PDO($dsn, $user, $password);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Błąd połączenia z bazą: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
}
