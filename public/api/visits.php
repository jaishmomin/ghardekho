<?php
declare(strict_types=1);

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require __DIR__ . '/../../vendor/autoload.php';

use App\Config\Database;
use App\Services\MailerService;

try {
    $pdo = Database::getConnection();
    $mailer = new MailerService();

    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        throw new Exception('Invalid JSON');
    }

    // Required fields
    $required = ['property_id', 'name', 'email', 'visit_type', 'visit_date'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("$field is required");
        }
    }

    // Insert visit
    $stmt = $pdo->prepare("
        INSERT INTO visits 
        (property_id, name, email, phone, visit_type, visit_date, status)
        VALUES (?, ?, ?, ?, ?, ?, 'pending')
    ");

    $stmt->execute([
        $data['property_id'],
        $data['name'],
        $data['email'],
        $data['phone'] ?? null,
        $data['visit_type'],
        $data['visit_date']
    ]);

    // Fetch property
    $pstmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
    $pstmt->execute([$data['property_id']]);
    $property = $pstmt->fetch();

    // Send confirmation email
    if ($property) {
        $mailer->sendVisitConfirmation(
            [
                'name' => $data['name'],
                'email' => $data['email'],
                'visit_type' => $data['visit_type'],
                'visit_date' => $data['visit_date']
            ],
            $property
        );
    }

    echo json_encode([
        'success' => true,
        'message' => 'Visit scheduled successfully'
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
