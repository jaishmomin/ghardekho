<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Config\Database;

$db = Database::getConnection();
$stmt = $db->query("SELECT COUNT(*) as total FROM properties");
echo json_encode($stmt->fetch());
