<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class MutationType
{
    private $type;

    public function __construct($conn, $orderType, $orderItemInputType)
    {
        $this->type = new ObjectType([
            'name' => 'Mutation',
            'fields' => [
                'createOrder' => [
                    'type' => $orderType,
                    'args' => [
                        'items' => Type::nonNull(Type::listOf($orderItemInputType)),
                        'userId' => ['type' => Type::nonNull(Type::string())],
                    ],
                    'resolve' => function ($rootValue, $args) use ($conn) {
                        // Delegate order creation to CreateOrderMutation
                        $mutation = new CreateOrderMutation($conn);
                        return $mutation->handle($rootValue, $args);
                    },
                ],
            ],
        ]);
    }

    /**
     * Get the GraphQL Mutation type object.
     *
     * @return ObjectType
     */
    public function getType(): ObjectType
    {
        return $this->type;
    }
}
