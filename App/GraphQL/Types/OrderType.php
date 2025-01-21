<?php 

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;

class OrderType
{
    private ObjectType $type;

    public function __construct()
    {
        $this->type = new ObjectType([
            'name' => 'Order',
            'fields' => [
                'orderId' => [
                    'type' => Type::nonNull(Type::string()),
                ],
                'status' => [
                    'type' => Type::nonNull(Type::string()),
                ],
                'orderTotal' => [
                    'type' => Type::nonNull(Type::float()),
                ],
                'orderTime' => [
                    'type' => Type::nonNull(Type::string()),
                ],
            ],
        ]);
    }

    public function getType(): ObjectType
    {
        return $this->type;
    }
}
