<?php
include 'funcs/connect.php';
session_start();
$id = $_SESSION['id'] ?? null;

if ($id) {
    $stmt = $conn->prepare("SELECT img FROM cseclubmembers WHERE studentID = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<h3>Uploaded Image:</h3>";
        echo "<img src='" . htmlspecialchars($row['img']) . "' width='300' alt='Uploaded Image'>";
    } else {
        echo "Image not found.";
    }
    $stmt->close();
} else {
    echo "Session expired or invalid access.";
}
