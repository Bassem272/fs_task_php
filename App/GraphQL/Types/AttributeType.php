<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use mysqli;

class AttributeType extends ObjectType
{
    public function __construct(AttributeItemType $attributeItemType, mysqli $conn)
    {
        parent::__construct([
            'name' => 'Attribute',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::string()),
                ],
                'product_id' => [
                    'type' => Type::nonNull(Type::string()),
                ],
                'name' => [
                    'type' => Type::string(),
                ],
                'type' => [
                    'type' => Type::string(),
                ],
                '__typename' => [
                    'type' => Type::string(),
                ],
                'attribute_items' => [
                    'type' => Type::listOf($attributeItemType),
                    'resolve' => function ($attribute, $args, $context) use ($conn) {
                        $query = "SELECT * FROM attribute_items WHERE attribute_id = ? AND product_id = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('ss', $attribute['id'], $attribute['product_id']);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        $attributeItems = [];
                        while ($item = $result->fetch_assoc()) {
                            $attributeItems[] = [
                                'id' => $item['id'],
                                'attribute_id' => $item['attribute_id'],
                                'product_id' => $item['product_id'],
                                'displayValue' => $item['displayValue'],
                                'value' => $item['value'],
                                '__typename' => $item['__typename'],
                            ];
                        }

                        return $attributeItems;
                    },
                ],
            ],
        ]);
    }
}
