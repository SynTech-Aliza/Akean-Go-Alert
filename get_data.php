<?php
header("Content-Type: application/json; charset=UTF-8");

// Your DirectAdmin Database Credentials
$host = "localhost";
$dbname = "your_database_name";
$username = "your_database_user";
$password = "your_database_password";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Change 'users' to your actual table name
    $stmt = $conn->prepare("SELECT * FROM users");
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["status" => "success", "data" => $data]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>