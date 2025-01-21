<?php

namespace App\GraphQL\Mutations;

use Exception;

class CreateOrderMutation
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function handle($rootValue, $args)
    {
        // Check for database connection error
        if ($this->conn->connect_error) {
            return $this->formatErrorResponse($this->conn->connect_error);
        }

        try {
            // Calculate the total amount
            $totalAmount = array_reduce($args['items'], function ($sum, $item) {
                return $sum + ($item['price'] * $item['quantity']);
            }, 0);

            $orderId = uniqid(); // Generate unique order ID
            $status = 'pending';

            // Insert order into the `orders` table
            $stmt = $this->conn->prepare(
                "INSERT INTO orders (orderId, userId, status, totalAmount) VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("sssd", $orderId, $args['userId'], $status, $totalAmount);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $this->insertOrderItems($orderId, $args['items']);

                // Successful response
                return [
                    'orderId' => $orderId,
                    'status' => $status,
                    'orderTotal' => $totalAmount,
                    'orderTime' => date("h:i:s A"), // Current time
                ];
            }

            return $this->formatErrorResponse('Failed to create the order.');
        } catch (Exception $e) {
            return $this->formatErrorResponse($e->getMessage());
        }
    }

    /**
     * Inserts items into the order_items table.
     */
    private function insertOrderItems($orderId, $items)
    {
        foreach ($items as $item) {
            $selectedAttributes = $this->encodeAttributes($item['selectedAttributes'] ?? null);

            $stmt = $this->conn->prepare(
                "INSERT INTO order_items (orderId, productId, name, price, quantity, selectedAttributes, categoryId, inStock)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sssdisis",
                $orderId,
                $item['productId'],
                $item['name'],
                $item['price'],
                $item['quantity'],
                $selectedAttributes,
                $item['categoryId'],
                $item['inStock']
            );

            $stmt->execute();
        }
    }

    /**
     * Encodes attributes as JSON if provided.
     */
    private function encodeAttributes($attributes)
    {
        return is_array($attributes) || is_object($attributes)
            ? json_encode($attributes)
            : $attributes;
    }

    /**
     * Formats an error response for GraphQL.
     */
    private function formatErrorResponse($message)
    {
        return [
            'orderId' => null,
            'status' => 'error',
            'orderTotal' => 0,
            'orderTime' => null,
            'message' => $message,
        ];
    }
}
