<?php

namespace App\Models; 
use App\Models\BaseModel; 
class CategoryModel extends BaseModel
{
    /**
     * Fetch all categories from the database with optional LIMIT.
     *
     * @param int|null $limit
     * @return array
     */
    public function getAllCategories($limit = null)
    {
        $categories = [];
        $query = 'SELECT id, name FROM categories';

        $params = [];
        $types = '';

        if ($limit !== null && is_numeric($limit)) {
            $query .= ' LIMIT ?';
            $params[] = (int)$limit;
            $types .= 'i';
        }

        $stmt = $this->executeQuery($query, $params, $types);
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $categories[] = $this->mapCategoryData($row);
        }

        $stmt->free_result();
        return $categories;
    }

    /**
     * Fetch a single category by ID from the database.
     *
     * @param int $id
     * @return array|null
     * @throws \InvalidArgumentException
     */
    public function getCategoryById($id)
    {
        if (!is_numeric($id)) {
            throw new \InvalidArgumentException("Invalid ID");
        }

        $query = 'SELECT id, name FROM categories WHERE id = ?';
        $stmt = $this->executeQuery($query, [(int)$id], 'i');
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row ? $this->mapCategoryData($row) : null;
    }

    /**
     * Map category data from database rows to output format.
     *
     * @param array $row
     * @return array
     */
    private function mapCategoryData($row)
    {
        return [
            'id' => (int)$row['id'],
            'name' => htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'),
        ];
    }
}