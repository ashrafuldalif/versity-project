<?php
include 'funcs/connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['photos'])) {
    header('Location: gallery.php');
    exit;
}

$row_id = (int)$_POST['row_id'];

// ---- Verify row exists ----
$chk = $conn->prepare("SELECT id FROM gallery_rows WHERE id=?");
$chk->bind_param('i', $row_id);
$chk->execute();
if ($chk->get_result()->num_rows === 0) {
    die('Invalid row');
}
$chk->close();

// ---- Create folder if missing ----
$dir = "uploads/gallery/rows/$row_id/";
if (!is_dir($dir)) mkdir($dir, 0777, true);

// ---- Insert each photo ----
$stmt = $conn->prepare("
    INSERT INTO gallery_photos (row_id, image_name, display_order)
    VALUES (?,?,?)
");

// get next order number
$orderRes = $conn->query("SELECT COALESCE(MAX(display_order),0)+1 FROM gallery_photos WHERE row_id=$row_id");
$nextOrder = $orderRes->fetch_row()[0];

foreach ($_FILES['photos']['tmp_name'] as $i => $tmp) {
    $orig = $_FILES['photos']['name'][$i];
    $ext  = pathinfo($orig, PATHINFO_EXTENSION);
    $new  = uniqid() . '.' . strtolower($ext);
    $dest = $dir . $new;

    if (move_uploaded_file($tmp, $dest)) {
        $stmt->bind_param('isi', $row_id, $new, $nextOrder);
        $stmt->execute();
        $nextOrder++;
    }
}
$stmt->close();

header('Location: gallery.php');
exit;
