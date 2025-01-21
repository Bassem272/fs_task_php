<?php

namespace App\GraphQL;

use GraphQL\GraphQL;
use GraphQL\Type\Schema;

class GraphQLServer
{
    private Schema $schema;

    /**
     * GraphQLServer constructor.
     *
     * Load the schema on server initialization.
     */
    public function __construct()
    {
        error_log("Inside GraphQLServer class");
        $this->schema = require __DIR__ . '/../schema.php';
    }

    /**
     * Handle the incoming GraphQL request.
     *
     * @param string $input The raw GraphQL query string
     *
     * @return array The query response as an associative array
     */
    public function handleRequest(string $input): array
    {
        // Decode the incoming GraphQL query
        $payload = json_decode($input, true);

        $query = $payload['query'] ?? '';
        $variables = $payload['variables'] ?? null;

        // Execute the query and return the result
        return $this->executeQuery($query, $variables);
    }

    /**
     * Execute the GraphQL query and return the result.
     *
     * @param string $query The GraphQL query string
     * @param array|null $variables The variables for the query
     *
     * @return array The query result as an associative array
     */
    private function executeQuery(string $query, ?array $variables): array
    {
        try {
            // Execute the GraphQL query using the loaded schema
            $result = GraphQL::executeQuery($this->schema, $query, null, null, $variables);

            return $result->toArray();
        } catch (\Exception $e) {
            // Return a standardized error response
            return [
                'errors' => [
                    ['message' => $e->getMessage()],
                ],
            ];
        }
    }
}
