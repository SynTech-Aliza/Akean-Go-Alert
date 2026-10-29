<?php
declare(strict_types=1);

require_once __DIR__ . '/api_common.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET' || ($_GET['health'] ?? '') !== '1') {
    api_json(404, ['status' => 'error', 'message' => 'Not found.']);
}

try {
    api_database();
    api_json(200, ['status' => 'success']);
} catch (Throwable $error) {
    api_log_error($error);
    api_json(503, [
        'status' => 'error',
        'message' => 'Database connection failed.',
    ]);
}
