<?php

namespace App\Database;

use mysqli;
use Dotenv\Dotenv;

class Connection
{
    private $conn;

    /**
     * Establish a connection to the database.
     *
     * @throws \Exception if connection fails
     */
    public function __construct()
    {

        // Load environment variables from the .env file
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../'); 
        $dotenv->load();

        // Get DB credentials from .env
        $hostname = $_ENV['DB_HOST'];
        $user = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASSWORD'];
        $database = $_ENV['DB_NAME'];
        $port = $_ENV['DB_PORT'] ?? 3306; // Default MySQL port is 3306

        // Create connection
        $this->conn = new mysqli($hostname, $user, $password, $database, $port);

        // Check connection and throw an exception on failure
        if ($this->conn->connect_error) {
            throw new \Exception('Connection failed: ' . $this->conn->connect_error);
        }
    }

    /**
     * Get the established database connection.
     *
     * @return mysqli
     */
    public function getConnection(): mysqli
    {
        return $this->conn;
    }
}
