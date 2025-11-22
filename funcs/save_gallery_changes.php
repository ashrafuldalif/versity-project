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

            $stmt = $conn->prepare("INSERT INTO gallery_rows (row_header, sub_header, order_num) VALUES (?, ?, ?)");
            if (!$stmt) {
                $errors[] = "Prepare failed for row creation: " . $conn->error;
                continue;
            }
            $stmt->bind_param('ssi', $header, $sub_header, $order_num);
            if (!$stmt->execute()) {
                $errors[] = "Failed to create row '$header': " . $stmt->error;
            } else {
                $successes[] = "✓ Created row: $header";
            }
            $stmt->close();
        }
    }

    // 2. Update row headers
    if (!empty($changes['rows']['updated_header'])) {
        foreach ($changes['rows']['updated_header'] as $update) {
            $rowId = $update['rowid'] ?? null;
            $value = $update['value'] ?? null;

            if (!$rowId || !$value) continue;

            $stmt = $conn->prepare("UPDATE gallery_rows SET row_header = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param('si', $value, $rowId);
                if ($stmt->execute()) {
                    $successes[] = "✓ Updated row $rowId header";
                } else {
                    $errors[] = "Failed to update row $rowId header: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }

    // 3. Update row sub-headers
    if (!empty($changes['rows']['updated_subheader'])) {
        foreach ($changes['rows']['updated_subheader'] as $update) {
            $rowId = $update['rowid'] ?? null;
            $value = $update['value'] ?? null;

            if (!$rowId) continue;

            $stmt = $conn->prepare("UPDATE gallery_rows SET sub_header = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param('si', $value, $rowId);
                if ($stmt->execute()) {
                    $successes[] = "✓ Updated row $rowId sub-header";
                } else {
                    $errors[] = "Failed to update row $rowId sub-header: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }

    // 4. Delete rows (cascade deletes images)
    if (!empty($changes['rows']['deleted'])) {
        foreach ($changes['rows']['deleted'] as $rowId) {
            // Delete associated images from disk first
            $stmt = $conn->prepare("SELECT image_name FROM gallery_photos WHERE row_id = ?");
            if ($stmt) {
                $stmt->bind_param('i', $rowId);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $filepath = $uploadDir . $row['image_name'];
                    if (file_exists($filepath)) {
                        unlink($filepath);
                    }
                }
                $stmt->close();
            }

            // Delete from database
            $stmt = $conn->prepare("DELETE FROM gallery_photos WHERE row_id = ?");
            if ($stmt) {
                $stmt->bind_param('i', $rowId);
                $stmt->execute();
                $stmt->close();
            }

            $stmt = $conn->prepare("DELETE FROM gallery_rows WHERE id = ?");
            if (!$stmt) {
                $errors[] = "Prepare failed for row deletion: " . $conn->error;
                continue;
            }
            $stmt->bind_param('i', $rowId);
            if (!$stmt->execute()) {
                $errors[] = "Failed to delete row $rowId: " . $stmt->error;
            } else {
                $successes[] = "✓ Deleted row $rowId";
            }
            $stmt->close();
        }
    }

    // ===== IMAGES HANDLING =====

    // 1. Insert uploaded images into database
    if (!empty($changes['images']['uploaded'])) {
        foreach ($changes['images']['uploaded'] as $img) {
            $rowId = $img['row_id'] ?? null;
            $imgId = $img['id'] ?? null;
            $name = $img['name'] ?? '';

            if (!$rowId || !$name) {
                $errors[] = "Invalid image data: missing row_id or name";
                continue;
            }

            // If imgId is provided, use it; otherwise let DB auto-increment
            if ($imgId) {
                $stmt = $conn->prepare("INSERT INTO gallery_photos (id, row_id, image_name, display_order) VALUES (?, ?, ?, ?)");
                if (!$stmt) {
                    $errors[] = "Prepare failed for image: " . $conn->error;
                    continue;
                }
                $displayOrder = 0;
                $stmt->bind_param('iisi', $imgId, $rowId, $name, $displayOrder);
            } else {
                $stmt = $conn->prepare("INSERT INTO gallery_photos (row_id, image_name, display_order) VALUES (?, ?, ?)");
                if (!$stmt) {
                    $errors[] = "Prepare failed for image: " . $conn->error;
                    continue;
                }
                $displayOrder = 0;
                $stmt->bind_param('isi', $rowId, $name, $displayOrder);
            }

            if (!$stmt->execute()) {
                $errors[] = "Failed to insert image '$name': " . $stmt->error;
            } else {
                $successes[] = "✓ Added image: $name to row $rowId";
            }
            $stmt->close();
        }
    }

    // 2. Delete images (from database and disk)
    if (!empty($changes['images']['deleted'])) {
        foreach ($changes['images']['deleted'] as $imgId) {
            $stmt = $conn->prepare("SELECT image_name FROM gallery_photos WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param('i', $imgId);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $filename = $row['image_name'];
                    $filepath = $uploadDir . $filename;

                    // Delete from disk
                    if (file_exists($filepath)) {
                        if (unlink($filepath)) {
                            $successes[] = "✓ Deleted file: $filename";
                        }
                    }
                }
                $stmt->close();
            }

            // Delete from database
            $stmt = $conn->prepare("DELETE FROM gallery_photos WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param('i', $imgId);
                if ($stmt->execute()) {
                    $successes[] = "✓ Removed image record ID $imgId";
                } else {
                    $errors[] = "Failed to delete image ID $imgId: " . $stmt->error;
                }
                $stmt->close();
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
