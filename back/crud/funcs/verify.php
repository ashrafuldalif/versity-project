<?php
session_start();
include '../funcs/connect.php';

if (!isset($_SESSION['id'])) {
    echo 'error';
    exit;
}

$entered = $_POST['verifypass'] ?? '';
$id      = $_SESSION['id'];

$stmt = $conn->prepare("SELECT pass FROM club_members WHERE id = ?");
$stmt->bind_param('s', $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

if ($row && password_verify($entered, $row['pass'])) {
    echo 'ok';
} else {
    echo 'wrong';
}
$stmt->close();
