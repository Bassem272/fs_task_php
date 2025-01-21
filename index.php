<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\GraphQL\GraphQLServer;


// CORS Handling 
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle CORS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0); // Allow preflight requests
}

try {
    // Initialize the GraphQL server and handle the incoming request
    $server = new GraphQLServer();
    $output = $server->handleRequest(file_get_contents('php://input'));
} catch (\Exception $e) {
    // Handle errors
    $output = [
        'errors' => [
            ['message' => $e->getMessage()],
        ],
    ];
}

// Set content-type to JSON for the response
header('Content-Type: application/json');

// Send the GraphQL query result as JSON
echo json_encode($output);
