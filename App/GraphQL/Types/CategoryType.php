<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;

class CategoryType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Category',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::string()),
                ],
                'name' => [
                    'type' => Type::nonNull(Type::string()),
                ],
            ],
        ]);
    }
}
