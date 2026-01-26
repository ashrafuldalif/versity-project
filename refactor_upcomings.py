#!/usr/bin/env python3
"""Refactor upcomings_crud.php to use admin_functions.php"""

import re

with open('admin/upcomings_crud.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Add admin_functions.php require and session_start after connect
content = content.replace(
    "include __DIR__ . '/../funcs/connect.php';",
    "include __DIR__ . '/../funcs/connect.php';\nrequire_once __DIR__ . '/../funcs/admin_functions.php';\n\nsession_start();"
)

# Remove the $message = ""; line
content = content.replace('$message = "";', '')

# Replace DELETE section
old_delete = r'''if \(isset\(\$_GET\['delete'\]\)\) \{
    \$id = \(int\)\$_GET\['delete'\];

    \$res = \$conn->query\("SELECT image FROM upcomings WHERE id=\$id"\);
    if \(\$row = \$res->fetch_assoc\(\)\) \{
        if \(\$row\['image'\] && file_exists\(\$uploadDir \. \$row\['image'\]\)\) \{
            unlink\(\$uploadDir \. \$row\['image'\]\);
        \}
    \}
    \$conn->query\("DELETE FROM upcomings WHERE id=\$id"\);

    header\("Location: upcomings_crud\.php"\);
    exit;
\}'''

new_delete = '''if (isset($_GET['delete'])) {
    deleteUpcoming($conn, $_GET['delete'], $uploadDir);
    redirectWithMessage('upcomings_crud.php', 'Event deleted successfully!');
}'''

# Use simpler string replacement instead of regex
content = content.replace(
    """if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $res = $conn->query("SELECT image FROM upcomings WHERE id=$id");
    if ($row = $res->fetch_assoc()) {
        if ($row['image'] && file_exists($uploadDir . $row['image'])) {
            unlink($uploadDir . $row['image']);
        }
    }
    $conn->query("DELETE FROM upcomings WHERE id=$id");

    header("Location: upcomings_crud.php");
    exit;
}""",
    """if (isset($_GET['delete'])) {
    deleteUpcoming($conn, $_GET['delete'], $uploadDir);
    redirectWithMessage('upcomings_crud.php', 'Event deleted successfully!');
}""")

# Replace TOGGLE section
content = content.replace(
    """if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];

    // Set all inactive
    $conn->query("UPDATE upcomings SET is_active=0");

    // Set selected active
    $conn->query("UPDATE upcomings SET is_active=1 WHERE id=$id");

    header("Location: upcomings_crud.php");
    exit;
}""",
    """if (isset($_GET['toggle'])) {
    toggleUpcomingActive($conn, $_GET['toggle']);
    redirectWithMessage('upcomings_crud.php', 'Status updated!');
}""")

# Replace LOAD EDIT DATA section
content = content.replace(
    """if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = $conn->query("SELECT * FROM upcomings WHERE id=$id");
    $edit = $res->fetch_assoc();
}""",
    """if (isset($_GET['edit'])) {
    $edit = getUpcomingById($conn, $_GET['edit']);
}""")

# Replace image upload logic
old_upload = """        if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($ext, $allowed)) {
            $newName = time() . "." . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newName);
            $image = $newName;
        }
    }"""

new_upload = """        if (!empty($_FILES['image']['name'])) {
        $uploaded = uploadUpcomingImage($_FILES['image'], $uploadDir, $image);
        if ($uploaded) {
            $image = $uploaded;
        }
    }"""

content = content.replace(old_upload, new_upload)

# Replace Insert/Update logic
old_crud = """    /* Insert or Update */
    if ($id) {
        $stmt = $conn->prepare("UPDATE upcomings SET heading=?, content=?, image=?, image_side=?, is_active=? WHERE id=?");
        $stmt->bind_param("ssssii", $heading, $content, $image, $image_side, $is_active, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO upcomings (heading, content, image, image_side, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $heading, $content, $image, $image_side, $is_active);
    }

    $stmt->execute();
    $stmt->close();

    header("Location: upcomings_crud.php");
    exit;"""

new_crud = """    /* Insert or Update */
    if ($id) {
        updateUpcoming($conn, $id, $heading, $content, $image, $image_side, $is_active);
        redirectWithMessage('upcomings_crud.php', 'Event updated successfully!');
    } else {
        createUpcoming($conn, $heading, $content, $image, $image_side, $is_active);
        redirectWithMessage('upcomings_crud.php', 'Event created successfully!');
    }"""

content = content.replace(old_crud, new_crud)

# Fix $image_side assignment (should allow "right" or default to "left")
content = content.replace(
    '$image_side = ($_POST[\'image_side\'] === "right") ? "right" : "left";',
    "$image_side = $_POST['image_side'] ?? 'left';"
)

# Add message getting and upcomings loading before HTML
content = content.replace(
    '?>\n<!DOCTYPE html>',
    '''$message = getSessionMessage();
$upcomings = getUpcomings($conn);
?>
<!DOCTYPE html>''')

# Fix message display HTML
content = content.replace(
    '<?php if ($message) echo $message; ?>',
    '''<?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>''')

# Replace the entire data fetching loop
old_loop = """            <?php
            $res = $conn->query("SELECT * FROM upcomings ORDER BY id DESC");
            while ($row = $res->fetch_assoc()):
            ?>

                <div class="col-12 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">

                            <div class="d-flex justify-content-between">
                                <h4><?= htmlspecialchars($row['heading']) ?></h4>

                                <?php if ($row['is_active']): ?>
                                    <span class="badge bg-danger d-flex align-items-center justify-content-center">IMPORTANT</span>
                                <?php endif; ?>
                            </div>

                            <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>

                            <?php if ($row['image']): ?>
                                <img src="../assets/upcomings/<?= $row['image'] ?>" style="height:120px" class="mb-3 rounded">
                            <?php endif; ?>

                            <a href="upcomings_crud.php?edit=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="upcomings_crud.php?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                onclick="return confirm('Delete event?')">Delete</a>

                            <a href="upcomings_crud.php?toggle=<?= $row['id'] ?>"
                                class="btn btn-warning btn-sm">
                                <?= $row['is_active'] ? "Remove IMPORTANT" : "Mark IMPORTANT" ?>
                            </a>

                        </div>
                    </div>
                </div>

            <?php endwhile; ?>"""

new_loop = """            <?php foreach ($upcomings as $row): ?>

                <div class="col-12 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h4><?= htmlspecialchars($row['heading']) ?></h4>
                                    <p class="text-muted"><?= htmlspecialchars(substr($row['content'], 0, 100)) ?>...</p>
                                </div>

                                <?php if ($row['is_active']): ?>
                                    <span class="badge bg-danger">IMPORTANT</span>
                                <?php endif; ?>
                            </div>

                            <?php if ($row['image']): ?>
                                <img src="../assets/upcomings/<?= htmlspecialchars($row['image']) ?>" style="height:120px" class="mb-3 rounded">
                            <?php endif; ?>

                            <div class="btn-group" role="group">
                                <a href="upcomings_crud.php?edit=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                                <a href="upcomings_crud.php?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Delete event?')">Delete</a>
                                <a href="upcomings_crud.php?toggle=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                                    <?= $row['is_active'] ? "Remove IMPORTANT" : "Mark IMPORTANT" ?>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>

            <?php endforeach; ?>

            <?php if (empty($upcomings)): ?>
                <div class="col-12">
                    <div class="alert alert-info">No events found. Create one to get started!</div>
                </div>
            <?php endif; ?>"""

content = content.replace(old_loop, new_loop)

# Fix image file input to accept only images
content = content.replace(
    '<input type="file" name="image" class="form-control mb-3">',
    '<input type="file" name="image" class="form-control mb-3" accept="image/*">')

# Fix form check-label to have id
content = content.replace(
    '''<input type="checkbox" class="form-check-input" name="is_active"
                            <?= ($edit['is_active'] ?? 0) ? "checked" : "" ?>>
                        <label class="form-check-label">Mark as IMPORTANT</label>''',
    '''<input type="checkbox" class="form-check-input" name="is_active" id="isActive"
                            <?= ($edit['is_active'] ?? 0) ? "checked" : "" ?>>
                        <label class="form-check-label" for="isActive">Mark as IMPORTANT</label>''')

# Add htmlspecialchars to all output in forms
content = content.replace(
    'value="<?= $edit[\'heading\'] ?? \'\' ?>"',
    'value="<?= htmlspecialchars($edit[\'heading\'] ?? \'\') ?>"')

content = content.replace(
    'textarea name="content" class="form-control mb-3" rows="4"><?= $edit[\'content\'] ?? \'\' ?></textarea>',
    'textarea name="content" class="form-control mb-3" rows="4"><?= htmlspecialchars($edit[\'content\'] ?? \'\') ?></textarea>')

content = content.replace(
    '<img src="../assets/upcomings/<?= $edit[\'image\'] ?>"',
    '<img src="../assets/upcomings/<?= htmlspecialchars($edit[\'image\']) ?>"')

# Write the refactored content
with open('admin/upcomings_crud.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("✓ Successfully refactored upcomings_crud.php to use admin_functions.php")
