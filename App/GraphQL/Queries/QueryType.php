<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use App\Models\ProductModel;
use App\Models\CategoryModel;

class QueryType
{
    private $productQuery;
    private $productsQuery;
    private $categoriesQuery;

    public function __construct($conn, $productType, $priceType, $galleryType, $attributeType, $categoryType)
    {
        $categoryModel = new CategoryModel($conn);
        $productModel = new ProductModel($conn);

        $this->productQuery = new ProductQuery($productModel, $productType);
        $this->productsQuery = new ProductsQuery($productModel, $productType);
        $this->categoriesQuery = new CategoriesQuery($categoryModel, $categoryType);
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
