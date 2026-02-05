<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /');
    exit;
}

require __DIR__ . '/../../vendor/autoload.php';

use App\Config\Database;
use App\Services\MailerService;

try {
    $pdo = Database::getConnection();
    $mailer = new MailerService();

    $propertyId = $_POST['property_id'] ?? null;
    if (!$propertyId) {
        throw new Exception('Property ID missing');
    }

    // Mark property as sold
    $pdo->prepare("
        UPDATE properties 
        SET status = 'sold'
        WHERE id = ?
    ")->execute([$propertyId]);

    // Get property info
    $pstmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
    $pstmt->execute([$propertyId]);
    $property = $pstmt->fetch();

    // Collect interested users (inquiries + visits)
    $emails = [];

    $stmt = $pdo->prepare("
        SELECT DISTINCT email FROM inquiries WHERE property_id = ?
        UNION
        SELECT DISTINCT email FROM visits WHERE property_id = ?
    ");
    $stmt->execute([$propertyId, $propertyId]);

    while ($row = $stmt->fetch()) {
        $emails[] = $row['email'];
    }

    // Send notification email
    foreach ($emails as $email) {
        $mailer->sendEmail(
            $email,
            'Property Sold – GharDekho',
            "
            <h2>Property Sold</h2>
            <p>The property <strong>{$property['title']}</strong> has been sold.</p>
            <p>Thank you for your interest.</p>
            "
        );
    }

    header('Location: visits.php');
    exit;

} catch (Throwable $e) {
    echo 'Error: ' . $e->getMessage();
}
