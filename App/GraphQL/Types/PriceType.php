<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;

class PriceType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Price',
            'fields' => [
                'product_id' => [
                    'type' => Type::nonNull(Type::string()), // Add product_id field (non-nullable)
                ],
                'amount' => [
                    'type' => Type::float(), // Amount field (nullable)
                ],
                'currency_label' => [
                    'type' => Type::nonNull(Type::string()), // Currency label field (non-nullable)
                ],
                'currency_symbol' => [
                    'type' => Type::string(), // Currency symbol field (nullable)
                ],
                '__typename' => [
                    'type' => Type::string(), // The typename field (nullable)
                ],
            ],
        ]);
    }
}
