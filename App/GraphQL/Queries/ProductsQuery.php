<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\ProductType;

class ProductsQuery
{
    private $conn;
    private $productType;

    public function __construct($conn, ProductType $productType)
    {
        $this->conn = $conn;
        $this->productType = $productType;
    }

    public function toGraphQL()
    {
        return [
            'products' => [
                'type' => Type::listOf($this->productType),
                'resolve' => function () {
                    return $this->resolveProductsQuery();
                },
            ],
        ];
    }

    private function resolveProductsQuery()
    {
        $result = $this->conn->query('SELECT * FROM products');
        $products = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $products[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'inStock' => (bool) $row['inStock'],
                    'description' => $row['description'],
                    'category_id' => $row['category_id'],
                    'brand' => $row['brand'],
                    '__typename' => $row['__typename'],
                ];
            }
            $result->free();
        }

        return $products;
    }
}
