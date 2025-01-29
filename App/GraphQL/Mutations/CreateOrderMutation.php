<?php

namespace App\GraphQL\Mutations;

use App\Models\OrderModel;

class CreateOrderMutation
{
    private $orderModel;

    public function __construct($conn)
    {
        $this->orderModel = new OrderModel($conn);
    }

    public function __invoke($_, array $args)
    {

        return $this->handle($_, $args);
    }

    public function handle($_, array $args)
    {
        $userId = $args['userId'];
        $items = $args['items'];

        // Input validation and call to createOrder
        $result = $this->orderModel->createOrder($userId, $items);

        if (isset($result['errors'])) {
            throw new \Exception($result['errors'][0]['message']);
        }

        return [
            'orderId' => $result['orderId'],
            'status' => $result['status'],
            'orderTotal' => $result['orderTotal'],
            'orderTime' => $result['orderTime'],
        ];
    }
}
