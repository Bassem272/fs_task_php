<?php

namespace App\Models;

use App\Models\BaseModel;
use mysqli;

class OrderModel extends BaseModel
{
    public function __construct(mysqli $conn)
    {
        parent::__construct($conn); // Call the parent constructor
    }

    public function createOrder(string $userId, array $items): array
    {
        try {
            // Start a transaction
            $this->conn->begin_transaction();

            // Validate and sanitize inputs
            $this->validateInput("User ID", $userId);

            foreach ($items as &$item) {
                $this->validateInput("Product Name", $item['name']);
                $this->validateInput("Product ID", $item['productId']);
                $this->validateInput("Category ID", $item['categoryId']);
                $item['selectedAttributes'] = $this->sanitizeAttributes($item['selectedAttributes']);
            }

            $totalAmount = $this->calculateTotalAmount($items);
            $orderId = uniqid();
            $status = 'pending';
            
            // Prepare and execute order insertion
            $stmt = $this->conn->prepare(
                "INSERT INTO orders (orderId, userId, status, totalAmount) VALUES (?, ?, ?, ?)"
            );
            if ($stmt === false) {
                throw new \Exception("Failed to prepare statement: " . $this->conn->error);
            }

            $stmt->bind_param("sssd", $orderId, $userId, $status, $totalAmount);
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                throw new \Exception("Failed to create order. No rows affected.");
            }

            // Insert items into the `order_items` table
            $this->insertOrderItems($orderId, $items);

            // Commit the transaction
            $this->conn->commit();

            return [
                'orderId' => $orderId,
                'status' => $status,
                'orderTotal' => $totalAmount,
                'orderTime' => date("h:i:s A"),
            ];
        } catch (\Exception $e) {
            // Rollback transaction on error
            $this->conn->rollback();
            error_log("Error in createOrder: " . $e->getMessage());

            return [
                'errors' => [
                    'message' => "Order creation failed: " . $e->getMessage(),
                ],
                'data' => null,
            ];
        }
    }

    private function calculateTotalAmount(array $items): float
    {
        return array_reduce($items, function ($total, $item) {
            return $total + ($item['price'] * $item['quantity']);
        }, 0.0);
    }

    private function insertOrderItems(string $orderId, array $items): void
    {
        foreach ($items as $item) {
            $attributes = $this->encodeAttributes($item['selectedAttributes']);

            $stmt = $this->conn->prepare(
                "INSERT INTO order_items (orderId, productId, name, price, quantity, selectedAttributes, categoryId, inStock)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            if ($stmt === false) {
                throw new \Exception("Failed to prepare statement: " . $this->conn->error);
            }

            $stmt->bind_param(
                "sssdisis",
                $orderId,
                $item['productId'],
                $item['name'],
                $item['price'],
                $item['quantity'],
                $attributes,
                $item['categoryId'],
                $item['inStock']
            );
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                throw new \Exception("Failed to insert order item for product ID {$item['productId']}.");
            }
        }
    }

    private function encodeAttributes($attributes): string
    {
        return is_string($attributes) ? $attributes : json_encode($attributes, JSON_UNESCAPED_UNICODE);
    }

    private function sanitizeAttributes($attributes): string
    {
        return is_string($attributes) ? htmlentities($attributes, ENT_QUOTES, 'UTF-8') : json_encode($attributes, JSON_UNESCAPED_UNICODE);
    }

    private function validateInput(string $fieldName, $input): void
    {
        if ($this->containsSQLInjection($input)) {
            throw new \Exception("Potential SQL Injection detected in {$fieldName}.");
        }

        // Basic sanitization
        $sanitizedInput = trim(htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));

        if (empty($sanitizedInput)) {
            throw new \Exception("Invalid {$fieldName}. The value is empty or improperly formatted.");
        }
    }

    private function containsSQLInjection($input): bool
    {
        // Common SQL injection patterns
        $patterns = [
            '/(;|\s)(SELECT|UPDATE|DELETE|INSERT|DROP|GRANT|UNION|--|#|\*|\bOR\b|\bAND\b|\bNOT\b)/i',
            '/(--|#).*/',
            '/;\s*$/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }
}
