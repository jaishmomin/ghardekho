<?php
// public/api/favorites.php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../src/config/Database.php';
require_once __DIR__ . '/../../src/controllers/FavoriteController.php';

use GharDekho\Controllers\FavoriteController;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$controller = new FavoriteController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo $controller->toggle();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo $controller->userFavorites();
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Not Found']);
}