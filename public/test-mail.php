<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Services\MailerService;

error_log('TEST FILE LOADED');

$mailer = new MailerService();

error_log('MAILER OBJECT CREATED');

$result = $mailer->sendEmail(
    'mominjaish10@gmail.com',
    'SMTP Test',
    '<h1>SMTP is working!</h1>'
);

echo $result ? 'Email sent' : 'Email failed';
