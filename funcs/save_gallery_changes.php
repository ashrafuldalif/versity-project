<?php
// Security check - must be logged in as admin
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Ensure JSON-only response and suppress PHP warnings/notices in output
ini_set('display_errors', '0');
error_reporting(0);
header('Content-Type: application/json');
include __DIR__ . '/connect.php';
require_once __DIR__ . '/admin_functions.php';

// This endpoint ONLY handles database updates
// Files are already in assets/gellary/ (uploaded by upload_gallery_images.php)

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

// Get changes JSON from POST
$changesJson = $_POST['changes'] ?? null;
if (!$changesJson) {
    echo json_encode(['success' => false, 'error' => 'No changes data']);
    exit;
}

$changes = json_decode($changesJson, true);
if (!$changes) {
    echo json_encode(['success' => false, 'error' => 'Invalid changes JSON']);
    exit;
}

$errors = [];
$successes = [];
$uploadDir = __DIR__ . '/../assets/gellary/';

try {
    // ===== ROWS HANDLING =====

    // 1. Create new rows
    if (!empty($changes['rows']['created'])) {
        foreach ($changes['rows']['created'] as $row) {
            $header = $row['header'] ?? '';
            $sub_header = $row['sub_header'] ?? '';
            $order_num = $row['order_num'] ?? 0;

            if (!$header) {
                $errors[] = "Cannot create row without header";
                continue;
            }

            $newId = createGalleryRow($conn, $header, $sub_header, $order_num);
            if ($newId === false) {
                $errors[] = "Failed to create row '$header'";
            } else {
                $successes[] = "✓ Created row: $header (ID: $newId)";
            }
        }
    }

    // 2. Update row headers
    if (!empty($changes['rows']['updated_header'])) {
        foreach ($changes['rows']['updated_header'] as $update) {
            $rowId = $update['rowid'] ?? null;
            $value = $update['value'] ?? null;

            if (!$rowId || !$value) continue;

            if (updateGalleryRow($conn, $rowId, $value, getGalleryRowById($conn, $rowId)['sub_header'] ?? '', 0)) {
                $successes[] = "✓ Updated row $rowId header";
            } else {
                $errors[] = "Failed to update row $rowId header";
            }
        }
    }

    // 3. Update row sub-headers
    if (!empty($changes['rows']['updated_subheader'])) {
        foreach ($changes['rows']['updated_subheader'] as $update) {
            $rowId = $update['rowid'] ?? null;
            $value = $update['value'] ?? null;

            if (!$rowId) continue;

            $current = getGalleryRowById($conn, $rowId) ?: [];
            $headerVal = $current['row_header'] ?? '';

            if (updateGalleryRow($conn, $rowId, $headerVal, $value, 0)) {
                $successes[] = "✓ Updated row $rowId sub-header";
            } else {
                $errors[] = "Failed to update row $rowId sub-header";
            }
        }
    }

    // 4. Delete rows (cascade deletes images)
    if (!empty($changes['rows']['deleted'])) {
        foreach ($changes['rows']['deleted'] as $rowId) {
            if (deleteGalleryRow($conn, $rowId)) {
                $successes[] = "✓ Deleted row $rowId";
            } else {
                $errors[] = "Failed to delete row $rowId";
            }
        }
    }

    // ===== IMAGES HANDLING =====

    // 1. Insert uploaded images into database
    if (!empty($changes['images']['uploaded'])) {
        foreach ($changes['images']['uploaded'] as $img) {
            $rowId = $img['row_id'] ?? null;
            $name = $img['name'] ?? '';

            if (!$rowId || !$name) {
                $errors[] = "Invalid image data: missing row_id or name";
                continue;
            }

            $photoId = addGalleryPhoto($conn, $rowId, $name, 0);
            if ($photoId === false) {
                $errors[] = "Failed to add image '$name' to row $rowId";
            } else {
                $successes[] = "✓ Added image: $name to row $rowId";
            }
        }
    }

    // 2. Delete images (from database and disk)
    if (!empty($changes['images']['deleted'])) {
        foreach ($changes['images']['deleted'] as $imgId) {
            if (deleteGalleryPhoto($conn, $imgId, $uploadDir)) {
                $successes[] = "✓ Removed image ID $imgId";
            } else {
                $errors[] = "Failed to delete image ID $imgId";
            }
        }
    }

    $conn->close();

    // Return result
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'errors' => $errors,
            'successes' => $successes
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'successes' => $successes,
            'message' => 'All changes saved successfully'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Exception: ' . $e->getMessage()
    ]);
}
