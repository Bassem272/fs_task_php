<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;

class QueryType
{
    private $conn;
    private $productQuery;
    private $productsQuery;
    private $categoriesQuery;

    public function __construct($conn, $productType, $priceType, $galleryType, $attributeType, $categoryType)
    {
        $this->conn = $conn;

        // Instantiate query objects with necessary dependencies
        $this->productQuery = new ProductQuery($conn, $productType, $priceType, $galleryType, $attributeType);
        $this->productsQuery = new ProductsQuery($conn, $productType);
        $this->categoriesQuery = new CategoriesQuery($conn, $categoryType);
    }

    public function toGraphQLObjectType(): ObjectType
    {
        return new ObjectType([
            'name' => 'Query',
            'fields' => function () {
                return array_merge(
                    $this->productQuery->toGraphQL(),
                    $this->productsQuery->toGraphQL(),
                    $this->categoriesQuery->toGraphQL()
                );
            },
        ]);
    }
}
