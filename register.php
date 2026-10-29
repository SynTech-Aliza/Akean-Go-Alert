<?php
declare(strict_types=1);

require_once __DIR__ . '/api_common.php';
api_require_post();

$input = api_read_json();
$firstName = trim((string) ($input['firstName'] ?? ''));
$middleName = trim((string) ($input['middleName'] ?? ''));
$lastName = trim((string) ($input['lastName'] ?? ''));
$contactNumber = trim((string) ($input['contactNumber'] ?? ''));
$contactPersonName = trim((string) ($input['contactPersonName'] ?? ''));
$contactPersonNumber = trim((string) ($input['contactPersonNumber'] ?? ''));
$email = strtolower(trim((string) ($input['email'] ?? '')));
$password = (string) ($input['password'] ?? '');

if ($firstName === '' || $lastName === '' || $contactNumber === '') {
    api_json(422, [
        'status' => 'error',
        'message' => 'First name, last name, and contact number are required.',
    ]);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    api_json(422, ['status' => 'error', 'message' => 'Enter a valid email address.']);
}
if (strlen($password) < 8 || strlen($password) > 72) {
    api_json(422, [
        'status' => 'error',
        'message' => 'Password must be between 8 and 72 characters.',
    ]);
}

$otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
$otpHash = password_hash($otp, PASSWORD_DEFAULT);
$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$connection = null;

try {
    $connection = api_database();
    $connection->beginTransaction();

    $existingUser = $connection->prepare(
        'SELECT 1 FROM users WHERE user_EmailAddress = :email LIMIT 1'
    );
    $existingUser->execute(['email' => $email]);
    if ($existingUser->fetchColumn()) {
        $connection->rollBack();
        api_json(409, [
            'status' => 'error',
            'code' => 'email_exists',
            'message' => 'An account with this email address already exists.',
        ]);
    }

    $insertUser = $connection->prepare(
        'INSERT INTO users (
            user_Profile,
            user_FirstName,
            user_MiddleName,
            user_LastName,
            user_ContactNumber,
            user_CPName,
            user_CPNumber,
            user_EmailAddress,
            user_Password,
            user_Status,
            user_OTP
        ) VALUES (
            :profile,
            :first_name,
            :middle_name,
            :last_name,
            :contact_number,
            :contact_person_name,
            :contact_person_number,
            :email,
            :password,
            0,
            :otp
        )'
    );
    $insertUser->execute([
        'profile' => 'Default.png',
        'first_name' => $firstName,
        'middle_name' => $middleName,
        'last_name' => $lastName,
        'contact_number' => $contactNumber,
        'contact_person_name' => $contactPersonName,
        'contact_person_number' => $contactPersonNumber,
        'email' => $email,
        'password' => $passwordHash,
        'otp' => $otpHash,
    ]);

    $saveOtpExpiry = $connection->prepare(
        'INSERT INTO user_signup_otp_meta (user_EmailAddress, expires_at, attempts)
         VALUES (:email, DATE_ADD(NOW(), INTERVAL 10 MINUTE), 0)'
    );
    $saveOtpExpiry->execute(['email' => $email]);

    api_send_otp($email, $otp);
    $connection->commit();

    api_json(201, [
        'status' => 'success',
        'message' => 'Verification code sent to your email address.',
    ]);
} catch (PDOException $error) {
    if ($connection instanceof PDO && $connection->inTransaction()) {
        $connection->rollBack();
    }
    api_log_error($error);

    if ($error->getCode() === '23000') {
        api_json(409, [
            'status' => 'error',
            'code' => 'email_exists',
            'message' => 'An account with this email address already exists.',
        ]);
    }

    api_json(500, [
        'status' => 'error',
        'message' => 'Could not create the account. Please try again later.',
    ]);
} catch (Throwable $error) {
    if ($connection instanceof PDO && $connection->inTransaction()) {
        $connection->rollBack();
    }
    api_log_error($error);
    api_json(503, [
        'status' => 'error',
        'message' => 'Could not complete registration. Check the server configuration.',
    ]);
}
