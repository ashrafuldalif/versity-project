<?php
session_start();
include '../funcs/connect.php';

if (!isset($_SESSION['id'])) {
    echo 'error';
    exit;
}
$id = $_SESSION['id'];

$name   = trim($_POST['name'] ?? '');
$batch  = trim($_POST['batch'] ?? '');
$mail   = trim($_POST['mail'] ?? '');
$phone  = trim($_POST['phone'] ?? '');
$blood  = trim($_POST['blood'] ?? '');
$pass   = $_POST['pass'] ?? '';

// Basic validation
if (!$name || !$batch || !$mail || !$phone || !$blood) {
    echo 'missing fields';
    exit;
}

// Build UPDATE
$sets = [];
$params = [];
$types  = '';

if ($name !== '') {
    $sets[] = "name = ?";
    $params[] = $name;
    $types .= 's';
}
if ($batch !== '') {
    $sets[] = "batch = ?";
    $params[] = $batch;
    $types .= 'i';
}
if ($mail !== '') {
    $sets[] = "mail = ?";
    $params[] = $mail;
    $types .= 's';
}
if ($phone !== '') {
    $sets[] = "phone = ?";
    $params[] = $phone;
    $types .= 's';
}
if ($blood !== '') {
    $sets[] = "bloodGroup = ?";
    $params[] = $blood;
    $types .= 's';
}
if ($pass !== '') {
    $sets[] = "pass = ?";
    $params[] = password_hash($pass, PASSWORD_DEFAULT);
    $types .= 's';
}

if (empty($sets)) {
    echo 'nothing to update';
    exit;
}

$sql = "UPDATE club_members SET " . implode(', ', $sets) . " WHERE id = ?";
$params[] = $id;
$types   .= 's';

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

echo $stmt->execute() ? 'success' : 'db error';
$stmt->close();
