<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\Type;

class ProductQuery
{
    private $conn;
    private $productType;

    public function __construct($conn, $productType)
    {
        $this->conn = $conn;
        $this->productType = $productType;
    }

    /**
     * Returns the GraphQL field definition for products query.
     */
    public function toGraphQL()
    {
        return [
            'product' => [
                'type' => $this->productType,
                'args' => [
                    'id' => Type::string(),
                    'name' => Type::string(),
                ],
                'resolve' => function ($root, $args, $context) {
                    return $this->resolveProductQuery($args);
                },
            ],
        ];
    }

    /**
     * Resolves the product query based on input arguments.
     */
    private function resolveProductQuery($args)
    {
        $query = "SELECT * FROM products";
        $params = [];
        $filters = [];

        if (!empty($args['id'])) {
            $filters[] = "id = ?";
            $params[] = $args['id'];
        }

        if (!empty($args['name'])) {
            $filters[] = "name = ?";
            $params[] = $args['name'];
        }

        if (!empty($filters)) {
            $query .= " WHERE " . implode(" AND ", $filters);
        }

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new \Exception('Failed to prepare SQL query: ' . $this->conn->error);
        }

        if ($params) {
            $stmt->bind_param(str_repeat("s", count($params)), ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc(); // Return a single product or null if not found
    }
}
