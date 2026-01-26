<?php

/**
 * ADMIN CRUD FUNCTIONS
 * Centralized database operations for admin panel
 */

// ============================================================================
// UPCOMINGS CRUD FUNCTIONS
// ============================================================================

/**
 * Get all upcomings ordered by creation date
 * @param mysqli $conn Database connection
 * @return array Array of upcoming events
 */
function getUpcomings($conn)
{
    $result = $conn->query("SELECT * FROM upcomings ORDER BY id DESC");
    if (!$result) {
        error_log("Database error: " . $conn->error);
        return [];
    }
    return $result->fetch_all(MYSQLI_ASSOC) ?: [];
}

/**
 * Get single upcoming by ID
 * @param mysqli $conn Database connection
 * @param int $id Upcoming ID
 * @return array|null Upcoming data or null if not found
 */
function getUpcomingById($conn, $id)
{
    $id = (int)$id;
    $stmt = $conn->prepare("SELECT * FROM upcomings WHERE id = ?");
    if (!$stmt) {
        error_log("Prepare error: " . $conn->error);
        return null;
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result;
}

/**
 * Create new upcoming event
 * @param mysqli $conn Database connection
 * @param string $heading Event heading
 * @param string $content Event content
 * @param string $image Image filename
 * @param string $imageSide Image position (left/right)
 * @param int $isActive Whether event is active
 * @return bool Success status
 */
function createUpcoming($conn, $heading, $content, $image, $imageSide, $isActive)
{
    // Only one active event allowed
    if ($isActive == 1) {
        $conn->query("UPDATE upcomings SET is_active = 0");
    }

    $imageSide = ($imageSide === "right") ? "right" : "left";

    $stmt = $conn->prepare(
        "INSERT INTO upcomings (heading, content, image, image_side, is_active) 
         VALUES (?, ?, ?, ?, ?)"
    );

    if (!$stmt) {
        error_log("Prepare error: " . $conn->error);
        return false;
    }

    $stmt->bind_param("ssssi", $heading, $content, $image, $imageSide, $isActive);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

/**
 * Update upcoming event
 * @param mysqli $conn Database connection
 * @param int $id Upcoming ID
 * @param string $heading Event heading
 * @param string $content Event content
 * @param string $image Image filename
 * @param string $imageSide Image position
 * @param int $isActive Whether event is active
 * @return bool Success status
 */
function updateUpcoming($conn, $id, $heading, $content, $image, $imageSide, $isActive)
{
    $id = (int)$id;

    // Only one active event allowed
    if ($isActive == 1) {
        $conn->query("UPDATE upcomings SET is_active = 0");
    }

    $imageSide = ($imageSide === "right") ? "right" : "left";

    $stmt = $conn->prepare(
        "UPDATE upcomings 
         SET heading = ?, content = ?, image = ?, image_side = ?, is_active = ? 
         WHERE id = ?"
    );

    if (!$stmt) {
        error_log("Prepare error: " . $conn->error);
        return false;
    }

    $stmt->bind_param("ssssii", $heading, $content, $image, $imageSide, $isActive, $id);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

/**
 * Delete upcoming event
 * @param mysqli $conn Database connection
 * @param int $id Upcoming ID
 * @param string $uploadDir Directory of files to delete
 * @return bool Success status
 */
function deleteUpcoming($conn, $id, $uploadDir)
{
    $id = (int)$id;
    $upcoming = getUpcomingById($conn, $id);

    if ($upcoming && $upcoming['image']) {
        $filePath = $uploadDir . $upcoming['image'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    $stmt = $conn->prepare("DELETE FROM upcomings WHERE id = ?");
    if (!$stmt) {
        error_log("Prepare error: " . $conn->error);
        return false;
    }

    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

/**
 * Toggle upcoming active status
 * @param mysqli $conn Database connection
 * @param int $id Upcoming ID to activate
 * @return bool Success status
 */
function toggleUpcomingActive($conn, $id)
{
    $id = (int)$id;

    // Deactivate all
    $conn->query("UPDATE upcomings SET is_active = 0");

    // Activate selected
    $stmt = $conn->prepare("UPDATE upcomings SET is_active = 1 WHERE id = ?");
    if (!$stmt) {
        error_log("Prepare error: " . $conn->error);
        return false;
    }

    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

/**
 * Handle upcoming image upload
 * @param array $fileArray $_FILES array
 * @param string $uploadDir Directory to upload to
 * @param string|null $currentImage Current image filename (to replace)
 * @return string|null New image filename or null if not uploaded
 */
function uploadUpcomingImage($fileArray, $uploadDir, $currentImage = null)
{
    if (empty($fileArray['name'])) {
        return null;
    }

    $ext = strtolower(pathinfo($fileArray['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($ext, $allowed)) {
        return null;
    }

    // Delete old image if exists
    if ($currentImage && file_exists($uploadDir . $currentImage)) {
        unlink($uploadDir . $currentImage);
    }

    $newName = time() . "." . $ext;
    if (move_uploaded_file($fileArray['tmp_name'], $uploadDir . $newName)) {
        return $newName;
    }

    return null;
}

// ============================================================================
// GALLERY CRUD FUNCTIONS
// ============================================================================

/**
 * Get all gallery rows
 * @param mysqli $conn Database connection
 * @return array Array of gallery rows
 */
function getGalleryRows($conn)
{
    $result = $conn->query(
        "SELECT id, row_header, sub_header, order_num 
         FROM gallery_rows 
         ORDER BY order_num ASC"
    );
    if (!$result) {
        error_log("Database error: " . $conn->error);
        return [];
    }
    return $result->fetch_all(MYSQLI_ASSOC) ?: [];
}

/**
 * Get gallery row by ID
 * @param mysqli $conn Database connection
 * @param int $id Row ID
 * @return array|null Row data
 */
function getGalleryRowById($conn, $id)
{
    $id = (int)$id;
    $stmt = $conn->prepare("SELECT * FROM gallery_rows WHERE id = ?");
    if (!$stmt) {
        error_log("Prepare error: " . $conn->error);
        return null;
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result;
}

/**
 * Create new gallery row
 * @param mysqli $conn Database connection
 * @param string $header Row header/title
 * @param string $subHeader Row sub-header
 * @param int $orderNum Display order
 * @return int|false Row ID or false on failure
 */
function createGalleryRow($conn, $header, $subHeader, $orderNum)
{
    $stmt = $conn->prepare(
        "INSERT INTO gallery_rows (row_header, sub_header, order_num) 
         VALUES (?, ?, ?)"
    );

    if (!$stmt) {
        error_log("Prepare error: " . $conn->error);
        return false;
    }

    $stmt->bind_param("ssi", $header, $subHeader, $orderNum);
    $success = $stmt->execute();
    $rowId = $stmt->insert_id;
    $stmt->close();

    return $success ? $rowId : false;
}

/**
 * Update gallery row
 * @param mysqli $conn Database connection
 * @param int $id Row ID
 * @param string $header Row header
 * @param string $subHeader Row sub-header
 * @param int $orderNum Display order
 * @return bool Success status
 */
function updateGalleryRow($conn, $id, $header, $subHeader, $orderNum)
{
    $id = (int)$id;
    $orderNum = (int)$orderNum;

    $stmt = $conn->prepare(
        "UPDATE gallery_rows 
         SET row_header = ?, sub_header = ?, order_num = ? 
         WHERE id = ?"
    );

    if (!$stmt) {
        error_log("Prepare error: " . $conn->error);
        return false;
    }

    $stmt->bind_param("ssii", $header, $subHeader, $orderNum, $id);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

/**
 * Delete gallery row
 * @param mysqli $conn Database connection
 * @param int $id Row ID
 * @return bool Success status
 */
function deleteGalleryRow($conn, $id)
{
    $id = (int)$id;

    // Delete associated images first
    deleteGalleryImagesByRowId($conn, $id);

    $stmt = $conn->prepare("DELETE FROM gallery_rows WHERE id = ?");
    if (!$stmt) {
        error_log("Prepare error: " . $conn->error);
        return false;
    }

    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

/**
 * Get all gallery photos
 * @param mysqli $conn Database connection
 * @return array Array of photos
 */
function getGalleryPhotos($conn)
{
    $result = $conn->query(
        "SELECT id, row_id, image_name, display_order 
         FROM gallery_photos 
         ORDER BY row_id, display_order ASC"
    );
    if (!$result) {
        error_log("Database error: " . $conn->error);
        return [];
    }
    return $result->fetch_all(MYSQLI_ASSOC) ?: [];
}

/**
 * Get gallery photos by row ID
 * @param mysqli $conn Database connection
 * @param int $rowId Row ID
 * @return array Array of photos in row
 */
function getGalleryPhotosByRowId($conn, $rowId)
{
    $rowId = (int)$rowId;
    $stmt = $conn->prepare(
        "SELECT id, row_id, image_name, display_order 
         FROM gallery_photos 
         WHERE row_id = ? 
         ORDER BY display_order ASC"
    );

    if (!$stmt) {
        error_log("Prepare error: " . $conn->error);
        return [];
    }

    $stmt->bind_param("i", $rowId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC) ?: [];
    $stmt->close();

    return $result;
}

/**
 * Add gallery photo
 * @param mysqli $conn Database connection
 * @param int $rowId Gallery row ID
 * @param string $imageName Image filename
 * @param int $displayOrder Display order
 * @return int|false Photo ID or false
 */
function addGalleryPhoto($conn, $rowId, $imageName, $displayOrder)
{
    $rowId = (int)$rowId;
    $displayOrder = (int)$displayOrder;

    $stmt = $conn->prepare(
        "INSERT INTO gallery_photos (row_id, image_name, display_order) 
         VALUES (?, ?, ?)"
    );

    if (!$stmt) {
        error_log("Prepare error: " . $conn->error);
        return false;
    }

    $stmt->bind_param("isi", $rowId, $imageName, $displayOrder);
    $success = $stmt->execute();
    $photoId = $stmt->insert_id;
    $stmt->close();

    return $success ? $photoId : false;
}

/**
 * Delete gallery photo
 * @param mysqli $conn Database connection
 * @param int $id Photo ID
 * @param string $uploadDir Directory containing photos
 * @return bool Success status
 */
function deleteGalleryPhoto($conn, $id, $uploadDir)
{
    $id = (int)$id;

    // Get photo data
    $stmt = $conn->prepare("SELECT image_name FROM gallery_photos WHERE id = ?");
    if (!$stmt) {
        error_log("Prepare error: " . $conn->error);
        return false;
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $photo = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Delete file
    if ($photo && file_exists($uploadDir . $photo['image_name'])) {
        unlink($uploadDir . $photo['image_name']);
    }

    // Delete from database
    $stmt = $conn->prepare("DELETE FROM gallery_photos WHERE id = ?");
    if (!$stmt) {
        error_log("Prepare error: " . $conn->error);
        return false;
    }

    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

/**
 * Delete all gallery photos in a row
 * @param mysqli $conn Database connection
 * @param int $rowId Row ID
 * @return bool Success status
 */
function deleteGalleryImagesByRowId($conn, $rowId)
{
    $rowId = (int)$rowId;

    $stmt = $conn->prepare("DELETE FROM gallery_photos WHERE row_id = ?");
    if (!$stmt) {
        error_log("Prepare error: " . $conn->error);
        return false;
    }

    $stmt->bind_param("i", $rowId);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

/**
 * Update gallery photo display order
 * @param mysqli $conn Database connection
 * @param int $id Photo ID
 * @param int $displayOrder New display order
 * @return bool Success status
 */
function updateGalleryPhotoOrder($conn, $id, $displayOrder)
{
    $id = (int)$id;
    $displayOrder = (int)$displayOrder;

    $stmt = $conn->prepare(
        "UPDATE gallery_photos SET display_order = ? WHERE id = ?"
    );

    if (!$stmt) {
        error_log("Prepare error: " . $conn->error);
        return false;
    }

    $stmt->bind_param("ii", $displayOrder, $id);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

// ============================================================================
// EXECUTIVES CRUD FUNCTIONS
// ============================================================================

/**
 * Get all approved executives
 * @param mysqli $conn Database connection
 * @return array Array of executives
 */
function getApprovedExecutives($conn)
{
    $sql = "
        SELECT e.id, e.name, e.email, e.photo,
               c.name AS club_name, c.id AS club_id,
               p.position_name, p.id AS position_id,
               e.batch, e.department
        FROM executives e
        LEFT JOIN clubs c ON e.club_id = c.id
        LEFT JOIN positions p ON e.position_id = p.id
        WHERE e.approved = 1
        ORDER BY e.id DESC
    ";

    $result = $conn->query($sql);
    if (!$result) {
        error_log("Database error: " . $conn->error);
        return [];
    }

    return $result->fetch_all(MYSQLI_ASSOC) ?: [];
}

/**
 * Get all pending executives
 * @param mysqli $conn Database connection
 * @return array Array of pending executives
 */
function getPendingExecutives($conn)
{
    $sql = "
        SELECT e.id, e.name, e.email, e.approved,
               c.name AS club_name,
               p.position_name
        FROM executives e
        LEFT JOIN clubs c ON e.club_id = c.id
        LEFT JOIN positions p ON e.position_id = p.id
        WHERE e.approved = 0
        ORDER BY e.id DESC
    ";

    $result = $conn->query($sql);
    if (!$result) {
        error_log("Database error: " . $conn->error);
        return [];
    }

    return $result->fetch_all(MYSQLI_ASSOC) ?: [];
}

/**
 * Get executive by ID
 * @param mysqli $conn Database connection
 * @param int $id Executive ID
 * @return array|null Executive data
 */
function getExecutiveById($conn, $id)
{
    $id = (int)$id;
    $sql = "
        SELECT e.*, c.name AS club_name, p.position_name
        FROM executives e
        LEFT JOIN clubs c ON e.club_id = c.id
        LEFT JOIN positions p ON e.position_id = p.id
        WHERE e.id = ?
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare error: " . $conn->error);
        return null;
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $result;
}

/**
 * Toggle executive approval status
 * @param mysqli $conn Database connection
 * @param int $id Executive ID
 * @return bool Success status
 */
function toggleExecutiveApproval($conn, $id)
{
    $id = (int)$id;

    $stmt = $conn->prepare("UPDATE executives SET approved = NOT approved WHERE id = ?");
    if (!$stmt) {
        error_log("Prepare error: " . $conn->error);
        return false;
    }

    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

/**
 * Delete executive
 * @param mysqli $conn Database connection
 * @param int $id Executive ID
 * @return bool Success status
 */
function deleteExecutive($conn, $id)
{
    $id = (int)$id;

    $stmt = $conn->prepare("DELETE FROM executives WHERE id = ?");
    if (!$stmt) {
        error_log("Prepare error: " . $conn->error);
        return false;
    }

    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

/**
 * Get all positions
 * @param mysqli $conn Database connection
 * @return array Array of positions
 */
function getPositions($conn)
{
    $result = $conn->query("SELECT id, position_name FROM positions ORDER BY id ASC");
    if (!$result) {
        error_log("Database error: " . $conn->error);
        return [];
    }
    return $result->fetch_all(MYSQLI_ASSOC) ?: [];
}

/**
 * Get position by ID
 * @param mysqli $conn Database connection
 * @param int $id Position ID
 * @return array|null Position data
 */
function getPositionById($conn, $id)
{
    $id = (int)$id;
    $stmt = $conn->prepare("SELECT * FROM positions WHERE id = ?");
    if (!$stmt) {
        error_log("Prepare error: " . $conn->error);
        return null;
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $result;
}

// ============================================================================
// UTILITY FUNCTIONS
// ============================================================================

/**
 * Redirect with message
 * @param string $location URL to redirect to
 * @param string|null $message Message to display (stored in session)
 * @return void
 */
function redirectWithMessage($location, $message = null)
{
    if ($message) {
        $_SESSION['message'] = $message;
    }
    header("Location: " . $location);
    exit;
}

/**
 * Get and clear session message
 * @return string|null Message if exists, null otherwise
 */
function getSessionMessage()
{
    $message = $_SESSION['message'] ?? null;
    if ($message) {
        unset($_SESSION['message']);
    }
    return $message;
}

/**
 * Validate file upload
 * @param array $fileArray $_FILES array
 * @param array $allowedExts Allowed extensions
 * @param int $maxSize Maximum file size in bytes
 * @return bool Whether file is valid
 */
function validateFileUpload($fileArray, $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'], $maxSize = 5242880)
{
    if (empty($fileArray['name'])) {
        return false;
    }

    if ($fileArray['size'] > $maxSize) {
        return false;
    }

    $ext = strtolower(pathinfo($fileArray['name'], PATHINFO_EXTENSION));
    return in_array($ext, $allowedExts);
}

/**
 * Get next display order for gallery row
 * @param mysqli $conn Database connection
 * @param int $rowId Row ID
 * @return int Next display order
 */
function getNextDisplayOrder($conn, $rowId)
{
    $rowId = (int)$rowId;
    $stmt = $conn->prepare("SELECT MAX(display_order) as max_order FROM gallery_photos WHERE row_id = ?");

    if (!$stmt) {
        return 1;
    }

    $stmt->bind_param("i", $rowId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return ($result['max_order'] ?? 0) + 1;
}
