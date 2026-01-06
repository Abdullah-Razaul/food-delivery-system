<?php
require_once __DIR__ . "/config/db.php";

$email = "admin@demo.com";
$newPass = "123456";
$hash = password_hash($newPass, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET role='admin', password_hash=? WHERE email=?");
$stmt->bind_param("ss", $hash, $email);
$stmt->execute();

echo "DONE. Admin password set to: 123456";
