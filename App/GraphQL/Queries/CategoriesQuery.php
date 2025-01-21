<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\CategoryType;

class CategoriesQuery
{
    private $conn;
    private $categoryType;

    public function __construct($conn, CategoryType $categoryType)
    {
        $this->conn = $conn;
        $this->categoryType = $categoryType;
    }

    /**
     * Convert the query to GraphQL query format.
     *
     * @return array
     */
    public function toGraphQL()
    {
        return [
            'categories' => [
                'type' => Type::listOf($this->categoryType),
                'resolve' => function ($root, $args, $context) {
                    return $this->resolveCategoriesQuery();
                },
            ],
        ];
    }

    /**
     * Resolves the query to fetch categories from the database.
     *
     * @return array
     */
    private function resolveCategoriesQuery()
    {
        try {
            $result = $this->conn->query('SELECT * FROM categories');
            $categories = [];

            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $categories[] = $this->mapCategoryData($row);
                }
                $result->free();
            }

            return $categories;
        } catch (\Exception $e) {
            // Log exception and/or handle it as needed
            return [
                'error' => 'Failed to fetch categories: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Maps category data from the database to the expected output structure.
     *
     * @param array $row
     * @return array
     */
    private function mapCategoryData($row)
    {
        return [
            'id' => $row['id'],
            'name' => $row['name'],
        ];
    }
}
