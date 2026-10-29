<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/connection.php';

function api_json(int $statusCode, array $payload): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

function api_require_post(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Allow: POST');
        api_json(405, ['status' => 'error', 'message' => 'Method not allowed.']);
    }
}

function api_read_json(): array
{
    $payload = json_decode(file_get_contents('php://input'), true);
    if (!is_array($payload)) {
        api_json(400, ['status' => 'error', 'message' => 'Invalid request data.']);
    }

    return $payload;
}

function api_database(): PDO
{
    return get_database_connection();
}

function api_send_otp(string $email, string $otp): void
{
    send_otp_email($email, $otp);
}

function api_log_error(Throwable $error): void
{
    error_log('[Akean Go-Alert API] ' . $error->getMessage());
}
