<?php
declare(strict_types=1);

namespace App\Services;

use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class MailerService
{
    private PHPMailer $mailer;
    private string $fromAddress;
    private string $fromName;

    public function __construct()
    {
        error_log('MAILER CONSTRUCTOR HIT');
        
        // .env is already usually loaded in the entry script,
        // but calling safeLoad here doesn't hurt in dev.
        if (empty($_ENV['SMTP_HOST'])) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
            $dotenv->safeLoad();
        }

        $this->mailer      = new PHPMailer(true);
        $this->fromAddress = $_ENV['SMTP_FROM_ADDR'] ?? 'info@ghardekho.com';
        $this->fromName    = $_ENV['SMTP_FROM_NAME'] ?? 'GharDekho';

        // Server settings
        $this->mailer->isSMTP();
        $this->mailer->Host       = $_ENV['SMTP_HOST'] ?? 'localhost';
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = $_ENV['SMTP_USER'] ?? '';
        $this->mailer->Password   = $_ENV['SMTP_PASS'] ?? '';
        $this->mailer->SMTPSecure = $_ENV['SMTP_SECURE'] ?? PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port       = (int)($_ENV['SMTP_PORT'] ?? 587);
        $this->mailer->SMTPDebug  = (int)($_ENV['SMTP_DEBUG'] ?? 0);

        // $this->mailer->SMTPDebug = 2;
        // $this->mailer->Debugoutput = function ($str, $level) {
        //     error_log("SMTP DEBUG: $str");
        // };
    }

    public function sendEmail($to, $subject, $body, $isHtml = true, $attachments = [])
    {
        try {
            // VERY IMPORTANT: reset recipients (PHPMailer bug otherwise)
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();

            // Sender
            $this->mailer->setFrom($this->fromAddress, $this->fromName);

            // Recipient
            if (is_array($to)) {
                foreach ($to as $email => $name) {
                    $this->mailer->addAddress($email, $name);
                }
            } else {
                $this->mailer->addAddress($to);
            }

            // Content
            $this->mailer->isHTML($isHtml);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);

            // FORCE SMTP SEND
            if (!$this->mailer->send()) {
                error_log('SMTP SEND ERROR: ' . $this->mailer->ErrorInfo);
                return false;
            }

            error_log('SMTP SUCCESS: Mail sent');
            return true;

        } catch (\Throwable $e) {
            error_log('MAILER EXCEPTION: ' . $e->getMessage());
            return false;
        }
    }

    public function sendInquiryNotification(int $ownerId, array $inquiry): bool
    {
        $subject = "New Property Inquiry";
        $body = "
            <h2>New Inquiry Received</h2>
            <p><strong>Name:</strong> {$inquiry['name']}</p>
            <p><strong>Email:</strong> {$inquiry['email']}</p>
            <p><strong>Message:</strong><br>{$inquiry['message']}</p>
        ";

        return $this->sendEmail($_ENV['SMTP_FROM_ADDR'], $subject, $body);
    }

    public function sendVisitScheduledNotification(int $ownerId, array $visit): bool
    {
        $subject = "New Visit Scheduled";
        $body = "
            <h2>Visit Scheduled</h2>
            <p><strong>Name:</strong> {$visit['name']}</p>
            <p><strong>Visit Type:</strong> {$visit['visit_type']}</p>
            <p><strong>Date:</strong> {$visit['visit_date']}</p>
        ";

        return $this->sendEmail($_ENV['SMTP_FROM_ADDR'], $subject, $body);
    }

    public function sendVisitStatusUpdate(int $userId, array $visit): bool
    {
        $subject = "Visit Status Updated";
        $body = "
            <h2>Visit Update</h2>
            <p>Your visit has been <strong>{$visit['status']}</strong>.</p>
            <p>Date: {$visit['visit_date']}</p>
        ";

        return $this->sendEmail($visit['email'], $subject, $body);
    }
    // ----------------- Specific email templates -----------------

    public function sendVerificationEmail(string $email, string $name, string $token): bool
    {
        $verificationUrl = rtrim($_ENV['APP_URL'] ?? 'http://localhost:8000', '/') .
            '/api/auth.php?action=verify&token=' . urlencode($token);

        $subject = 'Verify Your GharDekho Account';

        // Use HEREDOC to avoid escaping quotes
        $body = <<<HTML
<h2>Welcome to GharDekho, {$name}!</h2>
<p>Thank you for registering. Please verify your email address by clicking the button below:</p>
<p style="text-align: center; margin: 25px 0;">
    <a href="{$verificationUrl}" style="background-color: #4CAF50; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px;">
        Verify Email
    </a>
</p>
<p>If the button doesn't work, copy and paste this link into your browser:</p>
<p><small>{$verificationUrl}</small></p>
<p>This link will expire in 24 hours.</p>
HTML;

        return $this->sendEmail($email, $subject, $body, true);
    }

    public function sendPasswordResetEmail(string $email, string $name, string $token): bool
    {
        $resetUrl = rtrim($_ENV['APP_URL'] ?? 'http://localhost:8000', '/') .
            '/reset-password.php?token=' . urlencode($token);

        $subject = 'Password Reset Request';

        $body = <<<HTML
<h2>Hello {$name},</h2>
<p>We received a request to reset your password. Click the button below to set a new password:</p>
<p style="text-align: center; margin: 25px 0;">
    <a href="{$resetUrl}" style="background-color: #2196F3; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px;">
        Reset Password
    </a>
</p>
<p>If you didn't request this, you can safely ignore this email.</p>
<p><small>This link will expire in 1 hour.</small></p>
<p>If the button doesn't work, copy and paste this link into your browser:<br>
<small>{$resetUrl}</small></p>
HTML;

        return $this->sendEmail($email, $subject, $body, true);
    }

    public function sendInquiryConfirmation(array $inquiry, ?array $property = null): bool
    {
        $subject = 'Your Inquiry Has Been Received';

        $propertyInfo = '';
        if ($property) {
            $propertyInfo = <<<HTML
<h3>Property Details:</h3>
<p><strong>{$property['title']}</strong></p>
<p>Price: {$property['price']} {$property['currency']}</p>
<p>Location: {$property['address']}</p>
HTML;
        }

        $body = <<<HTML
<h2>Thank you for your inquiry, {$inquiry['name']}!</h2>
<p>We've received your message and will get back to you as soon as possible.</p>
{$propertyInfo}
<h3>Your Message:</h3>
<p>{$inquiry['message']}</p>
<p>Our team will contact you at {$inquiry['email']} or {$inquiry['phone']}.</p>
<p>Thank you for choosing GharDekho!</p>
HTML;

        return $this->sendEmail($inquiry['email'], $subject, $body, true);
    }

    public function sendVisitConfirmation(array $visit, array $property): bool
    {
        $formattedDate = date('F j, Y, g:i a', strtotime($visit['visit_date']));

        $subject = 'Visit Scheduled: ' . $property['title'];

        $body = <<<HTML
<h2>Your Visit Has Been Scheduled</h2>
<p>Hello {$visit['name']},</p>
<p>Your {$visit['visit_type']} visit has been scheduled successfully.</p>

<h3>Visit Details:</h3>
<p><strong>Property:</strong> {$property['title']}</p>
<p><strong>Date & Time:</strong> {$formattedDate}</p>
<p><strong>Type:</strong> {$visit['visit_type']} visit</p>

<p>Our representative will contact you shortly to confirm the details and provide any additional information.</p>

<p>If you need to reschedule or have any questions, please contact us at support@ghardekho.com</p>

<p>Thank you for choosing GharDekho!</p>
HTML;

        return $this->sendEmail($visit['email'], $subject, $body, true);
    }
}
