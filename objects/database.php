<?php
class Database
{
    private $host;
    private $username;
    private $password;
    private $database;
    private $connection;

    public function __construct($host, $username, $password, $database)
    {
        $this->host     = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->connect();
    }

    private function connect()
    {
        try {
            $this->connection = new PDO("mysql:host={$this->host};dbname={$this->database};charset=utf8", $this->username, $this->password);
            // Set the PDO error mode to exception
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            error_log($exception->getMessage()."\n", 3, __DIR__ . '/error.log');
            throw new Exception("Database connection error. Please try again later.");
            die();
        }
    }

    public function get_connection()
    {
        return $this->connection;
    }
}
