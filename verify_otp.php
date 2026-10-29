<?php
declare(strict_types=1);

require_once __DIR__ . '/api_common.php';
api_require_post();

$input = api_read_json();
$email = strtolower(trim((string) ($input['email'] ?? '')));
$otp = trim((string) ($input['otp'] ?? ''));

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^\d{6}$/', $otp)) {
    api_json(422, [
        'status' => 'error',
        'message' => 'Enter the email address and six-digit verification code.',
    ]);
}

$connection = null;
try {
    $connection = api_database();
    $connection->beginTransaction();

    $findUser = $connection->prepare(
        'SELECT user_Status, user_OTP
         FROM users
         WHERE user_EmailAddress = :email
         LIMIT 1
         FOR UPDATE'
    );
    $findUser->execute(['email' => $email]);
    $user = $findUser->fetch();

    if (!$user || (int) $user['user_Status'] !== 0) {
        $connection->rollBack();
        api_json(422, [
            'status' => 'error',
            'message' => 'The verification code is invalid or expired.',
        ]);
    }

    $findMeta = $connection->prepare(
        'SELECT expires_at, attempts, expires_at <= NOW() AS is_expired
         FROM user_signup_otp_meta
         WHERE user_EmailAddress = :email
         LIMIT 1
         FOR UPDATE'
    );
    $findMeta->execute(['email' => $email]);
    $meta = $findMeta->fetch();

    if (!$meta) {
        $connection->rollBack();
        api_json(422, [
            'status' => 'error',
            'message' => 'The verification code is invalid or expired.',
        ]);
    }

    if ((int) $meta['is_expired'] === 1) {
        $deletePendingUser = $connection->prepare(
            'DELETE FROM users
             WHERE user_EmailAddress = :email AND user_Status = 0'
        );
        $deletePendingUser->execute(['email' => $email]);
        $deleteMeta = $connection->prepare(
            'DELETE FROM user_signup_otp_meta WHERE user_EmailAddress = :email'
        );
        $deleteMeta->execute(['email' => $email]);
        $connection->commit();
        api_json(410, [
            'status' => 'error',
            'message' => 'The verification code expired. Please sign up again.',
        ]);
    }

    if ((int) $meta['attempts'] >= 5) {
        $connection->rollBack();
        api_json(429, [
            'status' => 'error',
            'message' => 'Too many incorrect codes. Please try signing up again later.',
        ]);
    }

    if (!password_verify($otp, (string) $user['user_OTP'])) {
        $incrementAttempts = $connection->prepare(
            'UPDATE user_signup_otp_meta
             SET attempts = attempts + 1
             WHERE user_EmailAddress = :email'
        );
        $incrementAttempts->execute(['email' => $email]);
        $connection->commit();
        api_json(422, [
            'status' => 'error',
            'message' => 'Incorrect verification code. Please check and try again.',
        ]);
    }

    $activateUser = $connection->prepare(
        'UPDATE users
         SET user_Status = 1, user_OTP = NULL
         WHERE user_EmailAddress = :email AND user_Status = 0'
    );
    $activateUser->execute(['email' => $email]);
    $deleteMeta = $connection->prepare(
        'DELETE FROM user_signup_otp_meta WHERE user_EmailAddress = :email'
    );
    $deleteMeta->execute(['email' => $email]);
    $connection->commit();

    api_json(200, [
        'status' => 'success',
        'message' => 'Email verified successfully.',
    ]);
} catch (Throwable $error) {
    if ($connection instanceof PDO && $connection->inTransaction()) {
        $connection->rollBack();
    }
    api_log_error($error);
    api_json(500, [
        'status' => 'error',
        'message' => 'Could not verify the code. Please try again later.',
    ]);
}
