<?php
// Security check - must be logged in as admin
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Return JSON only and suppress accidental HTML from warnings
ini_set('display_errors', '0');
error_reporting(0);
header('Content-Type: application/json');

// This endpoint handles file uploads and inserts DB records for each uploaded file.
// It expects:
// - files[] file inputs (multipart)
// - optional 'meta' POST field (JSON) mapping original filenames to row_id (array of { original, row_id, id })

include __DIR__ . '/connect.php';

$uploadDir = __DIR__ . '/../assets/gellary/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$errors = [];
$successes = [];
$uploadedFiles = []; // originalName => savedName
$inserted = []; // records of DB inserts: { original, saved, insert_id, row_id }

// Read meta mapping if provided
$metaJson = $_POST['meta'] ?? null;
$metaMap = [];
if ($metaJson) {
    $meta = json_decode($metaJson, true);
    if (is_array($meta)) {
        foreach ($meta as $m) {
            if (!empty($m['original'])) {
                $metaMap[$m['original']] = $m; // contains original, row_id, id optionally
            }
        }
    }
}

if (empty($_FILES['files'])) {
    echo json_encode(['success' => false, 'error' => 'No files uploaded']);
    exit;
}

try {
    $files = $_FILES['files'];
    $fileCount = count($files['name']);

    for ($i = 0; $i < $fileCount; $i++) {
        if ($files['error'][$i] !== 0) {
            $errors[] = "Error uploading {$files['name'][$i]}: " . $files['error'][$i];
            continue;
        }

        $originalName = basename($files['name'][$i]);
        $tmpPath = $files['tmp_name'][$i];

        // Validate file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $tmpPath);
        finfo_close($finfo);
        if (strpos($mimeType, 'image/') !== 0) {
            $errors[] = "$originalName is not an image (MIME: $mimeType)";
            continue;
        }

        // Decide target filename (unique)
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        $safeExt = preg_replace('/[^a-zA-Z0-9]/', '', $ext);
        if ($safeExt === '') $safeExt = 'jpg';
        $savedName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $safeExt;
        $destPath = $uploadDir . $savedName;

        // If meta provides a row_id, insert DB record first, then move file. If move fails, remove DB record.
        $rowId = null;
        if (isset($metaMap[$originalName]) && !empty($metaMap[$originalName]['row_id'])) {
            $rowId = (int)$metaMap[$originalName]['row_id'];
        }

        // Insert DB record (if rowId present) or insert with row_id = 0 (will be updated later by save step)
        $insertId = null;
        if ($rowId !== null) {
            $stmt = $conn->prepare("INSERT INTO gallery_photos (row_id, image_name, display_order) VALUES (?, ?, 0)");
            if ($stmt) {
                $stmt->bind_param('is', $rowId, $savedName);
                if ($stmt->execute()) {
                    $insertId = $stmt->insert_id;
                } else {
                    $errors[] = "DB insert failed for $originalName: " . $stmt->error;
                    $stmt->close();
                    continue;
                }
                $stmt->close();
            } else {
                $errors[] = "DB prepare failed: " . $conn->error;
                continue;
            }
        }

        // Move uploaded file
        if (move_uploaded_file($tmpPath, $destPath)) {
            $uploadedFiles[$originalName] = $savedName;
            $successes[] = "Uploaded: $originalName -> $savedName";
            $inserted[] = ['original' => $originalName, 'saved' => $savedName, 'insert_id' => $insertId, 'row_id' => $rowId];
        } else {
            $errors[] = "Failed to move file: $originalName";
            // rollback DB insert if any
            if ($insertId) {
                $stmt = $conn->prepare("DELETE FROM gallery_photos WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param('i', $insertId);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }

    echo json_encode([
        'success' => empty($errors),
        'uploaded' => $inserted,
        'uploadedFiles' => $uploadedFiles,
        'successes' => $successes,
        'errors' => $errors
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Exception: ' . $e->getMessage()]);
}
