<?php

use App\Database\Connection;
use App\GraphQL\Types\AttributeType;
use App\GraphQL\Types\AttributeItemType;
use App\GraphQL\Types\CategoryType;
use App\GraphQL\Types\GalleryType;
use App\GraphQL\Types\OrderItemInputType;
use App\GraphQL\Types\OrderType;
use App\GraphQL\Types\PriceType;
use App\GraphQL\Types\ProductType;
use App\GraphQL\Mutations\MutationType;
use App\GraphQL\Queries\QueryType;
use GraphQL\Type\Schema;

// Autoload dependencies
require_once __DIR__ . '/../vendor/autoload.php';

// Initialize the database connection
$connection = new Connection();
$conn = $connection->getConnection();

// Create attribute types
$attributeItemType = new AttributeItemType();
$attributeType = new AttributeType($attributeItemType, $conn);

// Create other necessary types
$galleryType = new GalleryType();
$priceType = new PriceType();
$categoryType = new CategoryType();

// Instantiate product types, passing necessary dependencies
$productType = new ProductType($conn, $priceType, $galleryType, $attributeType);

// Create the query type by passing created types
$queryType = new QueryType($conn, $productType, $priceType, $galleryType, $attributeType, $categoryType);

// Instantiate the Order and OrderItem types
$orderType = (new OrderType())->getType();
$orderItemInputType = (new OrderItemInputType())->getType();

// Instantiate the MutationType with the necessary arguments
$mutationType = new MutationType($conn, $orderType, $orderItemInputType);

// Create and return the GraphQL schema
return new Schema([
    'query' => $queryType->toGraphQLObjectType(),
    'mutation' => $mutationType->getType(),
]);
