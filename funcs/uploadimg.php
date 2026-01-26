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

// Enhanced security checks
$maxFileSize = 5 * 1024 * 1024; // 5MB
if ($file['size'] > $maxFileSize) {
    echo 'File too large. Maximum size is 5MB.';
    exit;
}

// Check file extension
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed = ['jpg', 'jpeg', 'png', 'gif'];
if (!in_array($ext, $allowed)) {
    echo 'Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.';
    exit;
}

// Check MIME type for additional security
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

$allowedMimes = [
    'image/jpeg',
    'image/jpg', 
    'image/png',
    'image/gif'
];

if (!in_array($mimeType, $allowedMimes)) {
    echo 'Invalid file type detected.';
    exit;
}

// Generate secure filename with timestamp
$timestamp = time();
$filename = $id . '_' . $timestamp . '.' . $ext;
$dest = __DIR__ . '/../assets/members/' . $filename;

// Ensure directory exists and is writable
$uploadDir = __DIR__ . '/../assets/members/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (move_uploaded_file($file['tmp_name'], $dest)) {
    // Update DB
    $stmt = $conn->prepare("UPDATE club_members SET img = ? WHERE id = ?");
    $stmt->bind_param('si', $filename, $id);
    
    if ($stmt->execute()) {
        echo 'Image updated successfully!';
    } else {
        echo 'Database update failed.';
        // Clean up uploaded file if DB update fails
        unlink($dest);
    }
    $stmt->close();
} else {
    echo 'Upload failed. Please try again.';
}
?>