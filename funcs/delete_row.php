<?php
include 'funcs/connect.php';

if (!isset($_POST['row_id'])) {
    header('Location: gallery.php');
    exit;
}

$row_id = (int)$_POST['row_id'];

// ---- Delete DB row (CASCADE removes photos) ----
$stmt = $conn->prepare("DELETE FROM gallery_rows WHERE id=?");
$stmt->bind_param('i', $row_id);
$stmt->execute();
$stmt->close();

// ---- Remove folder & files (optional but tidy) ----
$dir = "uploads/gallery/rows/$row_id/";
if (is_dir($dir)) {
    array_map('unlink', glob("$dir/*"));
    rmdir($dir);
}

header('Location: gallery.php');
exit;
