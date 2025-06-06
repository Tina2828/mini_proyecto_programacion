<?php

require_once __DIR__."/Log.php";

class DatabaseConnection {
    private $host;
    private $username;
    private $password;
    private $database;
    private $port;
    private $connection;

    public function __construct() {
        $this->host = getenv('DB_HOSTNAME') ?: 'localhost';
        $this->username = getenv('DB_USERNAME') ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: '';
        $this->database = getenv('DB_NAME') ?: 'test';
        $this->port = getenv('DB_PORT') ?: '3306';
    }

    public function connect() {
      Log::info("Connection to database {$this->database} at {$this->host}");
      $this->connection = new PDO("mysql:host={$this->host};dbname={$this->database};port={$this->port}", $this->username, $this->password);
      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_CLASS);
      Log::info("Connected to database {$this->database} at {$this->host}");
    }

    public function getConnection() {
        if (!$this->connection) {
            $this->connect();
        }
        return $this->connection;
    }

    public function disconnect() {
        Log::info("Disconnecting from database {$this->database} at {$this->host}");
        $this->connection = null;
    }
}
