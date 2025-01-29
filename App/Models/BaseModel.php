<?php 

namespace App\Models;

use App\Database\Connection; 

$connection = new Connection();
$conn = $connection->getConnection();
/**
 * Abstract BaseModel with common database logic.
 */
abstract class BaseModel
{
    protected $conn;

    public function __construct($conn)
    {

        $this->conn = $conn;
    }

    /**
     * Prepare and execute a query.
     *
     * @param string $query
     * @param array $params
     * @param string $types
     * @return \mysqli_stmt|null
     */
    protected function executeQuery($query, $params = [], $types = '')
    {
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            throw new \RuntimeException("Failed to prepare statement: " . $this->conn->error);
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            throw new \RuntimeException("Failed to execute query: " . $stmt->error);
        }

        return $stmt;
    }
}

