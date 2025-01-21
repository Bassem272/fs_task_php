<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;

class GalleryType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Gallery',
            'fields' => [
                'product_id' => [
                    'type' => Type::nonNull(Type::string()),
                ],
                'image_url' => [
                    'type' => Type::nonNull(Type::string()),
                ],
            ],
        ]);
    }
}
