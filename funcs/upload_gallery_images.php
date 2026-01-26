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

// This endpoint handles file uploads and associates files with rows when provided.
// It expects:
// - files[] file inputs (multipart)
// - optional 'meta' POST field (JSON) mapping original filenames to row_id (array of { original, row_id, id })

include __DIR__ . '/connect.php';
require_once __DIR__ . '/admin_functions.php';

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

        // Move uploaded file first
        if (!move_uploaded_file($tmpPath, $destPath)) {
            $errors[] = "Failed to move file: $originalName";
            continue;
        }

        // If meta provides a row_id, insert DB record now via admin function
        $rowId = null;
        if (isset($metaMap[$originalName]) && !empty($metaMap[$originalName]['row_id'])) {
            $rowId = (int)$metaMap[$originalName]['row_id'];
            $insertId = addGalleryPhoto($conn, $rowId, $savedName, 0);
            if ($insertId === false) {
                $errors[] = "DB insert failed for $originalName";
                // remove file since DB failed
                if (file_exists($destPath)) unlink($destPath);
                continue;
            }
        } else {
            $insertId = null; // will be handled in save step
        }

        $uploadedFiles[$originalName] = $savedName;
        $successes[] = "Uploaded: $originalName -> $savedName";
        $inserted[] = ['original' => $originalName, 'saved' => $savedName, 'insert_id' => $insertId, 'row_id' => $rowId];
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
