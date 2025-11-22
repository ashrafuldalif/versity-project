<?php
session_start();
include '../funcs/connect.php';

if (!isset($_SESSION['id'])) {
    echo 'Unauthorized';
    exit;
}
$id = $_SESSION['id'];

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
    echo 'No file uploaded';
    exit;
}

$file = $_FILES['image'];
$ext  = pathinfo($file['name'], PATHINFO_EXTENSION);
$allowed = ['jpg', 'jpeg', 'png', 'gif'];
if (!in_array(strtolower($ext), $allowed)) {
    echo 'Invalid file type';
    exit;
}

$filename = $id . '_' . '.' . $ext;
$dest     = __DIR__ . '/../assets/members/' . $filename;

if (move_uploaded_file($file['tmp_name'], $dest)) {
    // Update DB
    $stmt = $conn->prepare("UPDATE club_members SET img = ? WHERE id = ?");
    $stmt->bind_param('ss', $filename, $id);
    $stmt->execute();
    $stmt->close();
    echo 'Image updated!';
} else {
    echo 'Upload failed';
}
