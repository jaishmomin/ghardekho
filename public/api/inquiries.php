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
    $required = ['property_id', 'name', 'email', 'message'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("$field is required");
        }
    }

    // Insert inquiry
    $stmt = $pdo->prepare("
        INSERT INTO inquiries
        (property_id, name, email, phone, message, type, status)
        VALUES (?, ?, ?, ?, ?, 'contact', 'pending')
    ");

    $stmt->execute([
        $data['property_id'],
        $data['name'],
        $data['email'],
        $data['phone'] ?? null,
        $data['message']
    ]);

    // Fetch property (optional, for email)
    $pstmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
    $pstmt->execute([$data['property_id']]);
    $property = $pstmt->fetch();

    // Send confirmation email
    $mailer->sendInquiryConfirmation(
        [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? '',
            'message' => $data['message']
        ],
        $property ?: null
    );

    echo json_encode([
        'success' => true,
        'message' => 'Inquiry submitted successfully'
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
