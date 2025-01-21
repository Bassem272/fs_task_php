<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;

class AttributeItemType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'AttributeItem',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::string()),
                ],
                'attribute_id' => [
                    'type' => Type::nonNull(Type::string()),
                ],
                'product_id' => [
                    'type' => Type::nonNull(Type::string()),
                ],
                'displayValue' => [
                    'type' => Type::string(),
                ],
                'value' => [
                    'type' => Type::string(),
                ],
                '__typename' => [
                    'type' => Type::string(),
                ],
            ],
        ]);
    }
}
