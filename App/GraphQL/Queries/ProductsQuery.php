<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\ProductType;
use App\Models\ProductModel;

class ProductsQuery
{
    private $productModel;
    private $productType;

    public function __construct(ProductModel $productModel, $productType)
    {
        $this->productModel = $productModel;
        $this->productType = $productType;
    }

    /**
     * Returns the GraphQL field definition for products query.
     */
    public function toGraphQL()
    {
        return [
            'products' => [
                'type' => Type::listOf($this->productType),
                'args' =>[
                    'limit' => Type::int(),
                ],
                'resolve' => function ($root, $args, $context) {
                    $limit = isset($args['limit'])? (int)$args['limit'] : null; 

                    return $this->resolveProductsQuery($limit);
                },
            ],
        ];
    }

    /**
     * Resolves the products query to fetch all products.
     */
    private function resolveProductsQuery($limit)
    {
        try {
            return $this->productModel->getAllProducts($limit);
        } catch (\Exception $e) {
            return [
                'error' => 'Failed to fetch products: ' . $e->getMessage(),
            ];
        }
    }
}
