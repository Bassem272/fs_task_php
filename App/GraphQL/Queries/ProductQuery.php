<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\Type;
use App\Models\ProductModel;

class ProductQuery
{
    private $productModel;
    private $productType;

    public function __construct(ProductModel $productModel, $productType)
    {
        $this->productModel = $productModel;
        $this->productType = $productType;
    }

    /**
     * Returns the GraphQL field definition for the product query.
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
     * Resolves the product query by fetching data from the ProductModel.
     */
    private function resolveProductQuery($args)
    {
        return $this->productModel->getProductByFilters($args);
    }
}
