<?php
declare(strict_types=1);

// Configure these values locally. Never commit real passwords to source control.
const AKEAN_DATABASE = [
    'host' => 'localhost',
    'name' => 'akean_go-alert',
    'username' => 'Trikee_Server',
    'password' => '@#Trikee_2023',
];

const AKEAN_SMTP = [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'username' => 'mariaalizalomugdang@gmail.com',
    'password' => 'plqk obmq lubp cwzd',
    'from_email' => 'mariaalizalomugdang@gmail.com',
    'from_name' => 'Akean Go-Alert',
];

function get_database_connection(): PDO
{
    $host = AKEAN_DATABASE['host'];
    $database = AKEAN_DATABASE['name'];
    $username = AKEAN_DATABASE['username'];
    $password = AKEAN_DATABASE['password'];

    foreach ([$username, $password] as $setting) {
        if (str_starts_with($setting, 'YOUR_')) {
            throw new RuntimeException(
                'Replace the database placeholders in connection.php.'
            );
        }
    }

    return new PDO(
        "mysql:host={$host};dbname={$database};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
}

function send_otp_email(string $email, string $otp): void
{
    foreach ([
        AKEAN_SMTP['username'],
        AKEAN_SMTP['password'],
        AKEAN_SMTP['from_email'],
    ] as $setting) {
        if (str_starts_with($setting, 'YOUR_')) {
            throw new RuntimeException(
                'Replace the SMTP placeholders in connection.php.'
            );
        }
    }

    $autoload = __DIR__ . '/vendor/autoload.php';
    if (!is_file($autoload)) {
        throw new RuntimeException('PHPMailer Composer autoload was not found.');
    }
    require_once $autoload;

    $mailer = new PHPMailer\PHPMailer\PHPMailer(true);
    $mailer->isSMTP();
    $mailer->Host = AKEAN_SMTP['host'];
    $mailer->SMTPAuth = true;
    $mailer->Username = AKEAN_SMTP['username'];
    $mailer->Password = AKEAN_SMTP['password'];
    $mailer->Port = AKEAN_SMTP['port'];
    $mailer->SMTPSecure = AKEAN_SMTP['port'] === 465
        ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS
        : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mailer->CharSet = PHPMailer\PHPMailer\PHPMailer::CHARSET_UTF8;
    $mailer->setFrom(AKEAN_SMTP['from_email'], AKEAN_SMTP['from_name']);
    $mailer->addAddress($email);
    $mailer->isHTML(true);
    $mailer->Subject = 'Your Akean Go-Alert verification code';
    $mailer->Body = '<p>Your Akean Go-Alert verification code is:</p>'
        . '<p style="font-size:24px;font-weight:bold;letter-spacing:4px">'
        . htmlspecialchars($otp, ENT_QUOTES, 'UTF-8')
        . '</p><p>This code expires in 10 minutes. If you did not request it, '
        . 'you can ignore this email.</p>';
    $mailer->AltBody = "Your Akean Go-Alert verification code is {$otp}. "
        . 'It expires in 10 minutes.';
    $mailer->send();
}
