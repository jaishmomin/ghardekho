<?php
// public/api/cities.php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../src/config/Database.php';

use GharDekho\Models\City;

header('Content-Type: application/json');

try {
    $cities = (new City())->all();
    echo json_encode([
        'success' => true,
        'data' => $cities
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}