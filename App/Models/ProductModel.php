<?php

namespace App\Models;

use App\Models\BaseModel; 
class ProductModel extends BaseModel
{
    /**
     * Retrieves a single product based on filters.
     *
     * @param array $filters Associative array of filters (id, name, etc.)
     * @return array|null The product or null if not found.
     * @throws \InvalidArgumentException if filters are not valid.
     */
    public function getProductByFilters(array $filters)
    {
        $query = "SELECT * FROM products";
        $params = [];
        $types = ""; // Parameter types
        $conditions = [];

        // Apply filters
        if (isset($filters['id'])) {
            if (!is_string($filters['id'])) {
                throw new \InvalidArgumentException('The "id" filter must be an integer.');
            }
            $conditions[] = "id = ?";
            $params[] = $filters['id'];
            $types .= "s";
        }
        $this->validateInput("User Id", $filters['id']); 
        
        
        if ($conditions) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        // Execute the query
        $stmt = $this->executeQuery($query, $params, $types);
        $result = $stmt->get_result();

        return $result->fetch_assoc() ?: null; // Return the result or null
    }

    /**
     * Retrieves all products with optional limit.
     *
     * @param int|null $limit Number of products to retrieve.
     * @return array List of products.
     * @throws \InvalidArgumentException if the limit is not a positive integer.
     */
    public function getAllProducts(?int $limit = null)
    {
        $query = "SELECT * FROM products";
        $params = [];
        $types = "";

        // Append LIMIT clause if a limit is provided
        if ($limit !== null) {
            if ($limit <= 0) {
                throw new \InvalidArgumentException("The limit must be a positive integer.");
            }
            $query .= " LIMIT ?";
            $params[] = $limit;
            $types .= "i";
        }

        // Execute the query
        $stmt = $this->executeQuery($query, $params, $types);
        $result = $stmt->get_result();
        $products = [];

        while ($row = $result->fetch_assoc()) {
            $products[] = $row; // Add each product to the list
        }

        return $products;
    }

    private function validateInput(string $fieldName, $input): void
    {
        if ($this->containsSQLInjection($input)) {
            throw new \Exception("Potential SQL Injection detected in {$fieldName}.");
        }

        // Basic sanitization
        $sanitizedInput = trim(htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));

        if (empty($sanitizedInput)) {
            throw new \Exception("Invalid {$fieldName}. The value is empty or improperly formatted.");
        }
    }

    private function containsSQLInjection($input): bool
    {
        // Common SQL injection patterns
        $patterns = [
            '/(;|\s)(SELECT|UPDATE|DELETE|INSERT|DROP|GRANT|UNION|--|#|\*|\bOR\b|\bAND\b|\bNOT\b)/i',
            '/(--|#).*/',
            '/;\s*$/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }
}
