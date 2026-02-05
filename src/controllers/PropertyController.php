<?php

namespace App\Controllers;

use App\Config\Database;
use App\Models\Property;
use App\Services\JWTAuth;
use Exception;

class PropertyController
{
    private $auth;

    public function __construct()
    {
        $this->auth = new JWTAuth();
    }

    /**
     * PUBLIC SEARCH (NO AUTH REQUIRED)
     * GET /api/properties.php?city=&type=&budget_min=&budget_max=
     */
    public function index()
{
    $db = Database::getConnection();

    $city = $_GET['city'] ?? null;
    $type = $_GET['type'] ?? null;
    $min  = $_GET['budget_min'] ?? null;
    $max  = $_GET['budget_max'] ?? null;

    $sql = "
        SELECT p.*, c.name AS city
        FROM properties p
        LEFT JOIN cities c ON p.city_id = c.id
        WHERE p.status = 'available'
    ";

    $params = [];

    if ($city) {
        $sql .= " AND c.name = :city";
        $params[':city'] = $city;
    }

    if ($type) {
        $sql .= " AND p.type = :type";
        $params[':type'] = $type;
    }

    if ($min) {
        $sql .= " AND p.price >= :min";
        $params[':min'] = $min;
    }

    if ($max) {
        $sql .= " AND p.price <= :max";
        $params[':max'] = $max;
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    $properties = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'count'   => count($properties),
        'data'    => $properties
    ]);
    exit;
}
}