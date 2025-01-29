<?php

namespace App\GraphQL\Queries;

use App\Database\Connection;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\CategoryType;
use App\Models\CategoryModel;

$connection = new Connection();
$conn = $connection->getConnection();

class CategoriesQuery
{
    private $categoryModel;
    private $categoryType;

    public function __construct(CategoryModel $categoryModel, CategoryType $categoryType)
    {
        $this->categoryModel = $categoryModel;
        $this->categoryType = $categoryType;
    }

    /**
     * Convert the query to GraphQL query format.
     *
     * @return array
     */
    public function toGraphQL()
    {
        return [
            'categories' => [
                'type' => Type::listOf($this->categoryType),
                'args' => [
                    'limit' => Type::int(),
                ],
                'resolve' => function ($root, $args) {
                    $limit = isset($args['limit']) ? (int)$args['limit'] : null;
                    return $this->categoryModel->getAllCategories($limit);
                },
            ],
            'category' => [
                'type' => $this->categoryType,
                'args' => [
                    'id' => Type::nonNull(Type::int()),
                ],
                'resolve' => function ($root, $args) {
                    return $this->categoryModel->getCategoryById($args['id']);
                },
            ],
        ];
    }
}
