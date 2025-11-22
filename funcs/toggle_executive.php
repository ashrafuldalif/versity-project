<?php
// Security check - must be logged in as admin
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');
include __DIR__ . '/connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$active = isset($_POST['active']) ? (int)$_POST['active'] : null;

if (!$id || !is_numeric($id) || $active === null) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

// Update active flag
$stmt = $conn->prepare('UPDATE executives SET active = ? WHERE id = ?');
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Prepare failed']);
    exit;
}
$stmt->bind_param('ii', $active, $id);
$ok = $stmt->execute();
$stmt->close();
$conn->close();

if ($ok) {
    echo json_encode(['success' => true, 'id' => $id, 'active' => $active]);
} else {
    echo json_encode(['success' => false, 'error' => 'DB update failed']);
}
