#!/usr/bin/env python3
"""Refactor save_gallery_changes.php to use admin_functions.php"""

with open('funcs/save_gallery_changes.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Add admin_functions include after connect
content = content.replace("include __DIR__ . '/connect.php';", "include __DIR__ . '/connect.php';\nrequire_once __DIR__ . '/admin_functions.php';")

# Replace large DB handling blocks with calls to admin functions
# We'll replace specific sections to minimize risk

# Replace create rows block
content = content.replace(
    """    // 1. Create new rows
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
""",
    """    // 1. Create new rows
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
"""
)

# Replace update header block
content = content.replace(
    """    // 2. Update row headers
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
""",
    """    // 2. Update row headers
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
"""
)

# Replace update subheader block
content = content.replace(
    """    // 3. Update row sub-headers
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
""",
    """    // 3. Update row sub-headers
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
"""
)

# Replace delete rows block
content = content.replace(
    """    // 4. Delete rows (cascade deletes images)
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
""",
    """    // 4. Delete rows (cascade deletes images)
    if (!empty($changes['rows']['deleted'])) {
        foreach ($changes['rows']['deleted'] as $rowId) {
            if (deleteGalleryRow($conn, $rowId)) {
                $successes[] = "✓ Deleted row $rowId";
            } else {
                $errors[] = "Failed to delete row $rowId";
            }
        }
    }
"""
)

# Replace images uploaded block
content = content.replace(
    """    // 1. Insert uploaded images into database
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
""",
    """    // 1. Insert uploaded images into database
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
"""
)

# Replace images deleted block
content = content.replace(
    """    // 2. Delete images (from database and disk)
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
""",
    """    // 2. Delete images (from database and disk)
    if (!empty($changes['images']['deleted'])) {
        foreach ($changes['images']['deleted'] as $imgId) {
            if (deleteGalleryPhoto($conn, $imgId, $uploadDir)) {
                $successes[] = "✓ Removed image ID $imgId";
            } else {
                $errors[] = "Failed to delete image ID $imgId";
            }
        }
    }
"""
)

# Write back
with open('funcs/save_gallery_changes.php', 'w', encoding='utf-8') as f:
    f.write(content)

print('✓ Refactored save_gallery_changes.php')
