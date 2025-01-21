<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;

class ProductType extends ObjectType
{
    public function __construct($conn, $priceType, $galleryType, $attributeType)
    {
        parent::__construct([
            'name' => 'Product',
            'fields' => [
                'id' => ['type' => Type::nonNull(Type::string())],
                'name' => ['type' => Type::nonNull(Type::string())],
                'inStock' => ['type' => Type::nonNull(Type::boolean())],
                'description' => ['type' => Type::string()],
                'category_id' => ['type' => Type::string()],
                'brand' => ['type' => Type::string()],
                '__typename' => ['type' => Type::string()],
                
                // Resolved price field
                'price' => [
                    'type' => Type::listOf($priceType),
                    'resolve' => function ($product, $args, $context) use ($conn) {
                        return $this->resolvePrices($conn, $product['id']);
                    }
                ],

                // Resolved gallery field
                'gallery' => [
                    'type' => Type::listOf($galleryType),
                    'resolve' => function ($product, $args, $context) use ($conn) {
                        return $this->resolveGallery($conn, $product['id']);
                    }
                ],

                // Resolved attributes field
                'attributes' => [
                    'type' => Type::listOf($attributeType),
                    'resolve' => function ($product, $args, $context) use ($conn) {
                        return $this->resolveAttributes($conn, $product['id']);
                    }
                ],
            ],
        ]);
    }

    /**
     * Resolves the prices associated with a product
     *
     * @param mysqli $conn
     * @param string $productId
     * @return array
     */
    private function resolvePrices($conn, $productId)
    {
        $query = "SELECT * FROM prices WHERE product_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $productId);
        $stmt->execute();
        $result = $stmt->get_result();

        $prices = [];
        while ($price = $result->fetch_assoc()) {
            $prices[] = [
                'product_id' => $price['product_id'],
                'amount' => $price['amount'],
                'currency_label' => $price['currency_label'],
                'currency_symbol' => $price['currency_symbol'],
                '__typename' => $price['__typename'],
            ];
        }
        return $prices;
    }

    /**
     * Resolves the gallery images associated with a product
     *
     * @param mysqli $conn
     * @param string $productId
     * @return array
     */
    private function resolveGallery($conn, $productId)
    {
        $query = "SELECT * FROM product_gallery WHERE product_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $productId);
        $stmt->execute();
        $result = $stmt->get_result();

        $gallery = [];
        while ($row = $result->fetch_assoc()) {
            $gallery[] = [
                'product_id' => $row['product_id'],
                'image_url' => $row['image_url'],
            ];
        }
        return $gallery;
    }

    /**
     * Resolves the attributes associated with a product
     *
     * @param mysqli $conn
     * @param string $productId
     * @return array
     */
    private function resolveAttributes($conn, $productId)
    {
        $query = "SELECT * FROM attributes WHERE product_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $productId);
        $stmt->execute();
        $result = $stmt->get_result();

        $attributes = [];
        while ($attribute = $result->fetch_assoc()) {
            $attributes[] = $attribute;
        }
        return $attributes;
    }
}
