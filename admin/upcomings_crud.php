<?php
// Security check - must be logged in as admin
require_once __DIR__ . '/../funcs/check_admin.php';
include __DIR__ . '/../funcs/connect.php';
require_once __DIR__ . '/../funcs/admin_functions.php';

session_start();

$uploadDir = __DIR__ . '/../assets/upcomings/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);



/* -------------------------
   DELETE
-------------------------- */
if (isset($_GET['delete'])) {
    deleteUpcoming($conn, $_GET['delete'], $uploadDir);
    redirectWithMessage('upcomings_crud.php', 'Event deleted successfully!');
}

/* -------------------------
   TOGGLE IMPORTANT
-------------------------- */
if (isset($_GET['toggle'])) {
    toggleUpcomingActive($conn, $_GET['toggle']);
    redirectWithMessage('upcomings_crud.php', 'Status updated!');
}

/* -------------------------
   LOAD EDIT DATA
-------------------------- */
$edit = null;
if (isset($_GET['edit'])) {
    $edit = getUpcomingById($conn, $_GET['edit']);
}

/* -------------------------
   ADD / UPDATE FORM
-------------------------- */
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $id         = $_POST['id'] ?? null;
    $heading    = $_POST['heading'];
    $content    = $_POST['content'];
    $image_side = $_POST['image_side'] ?? 'left';
    $is_active  = isset($_POST['is_active']) ? 1 : 0;

    // enforce only one active
    if ($is_active == 1) {
        $conn->query("UPDATE upcomings SET is_active=0");
    }

    /* Upload Image */
    $image = $edit['image'] ?? "";

    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($ext, $allowed)) {
            $newName = time() . "." . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newName);
            $image = $newName;
        }
    }

    /* Insert or Update */
    if ($id) {
        updateUpcoming($conn, $id, $heading, $content, $image, $image_side, $is_active);
        redirectWithMessage('upcomings_crud.php', 'Event updated successfully!');
    } else {
        createUpcoming($conn, $heading, $content, $image, $image_side, $is_active);
        redirectWithMessage('upcomings_crud.php', 'Event created successfully!');
    }
}
$message = getSessionMessage();
$upcomings = getUpcomings($conn);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Upcomings CRUD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/admidMembers.css" rel="stylesheet">

</head>

<body class="bg-light">
    <div class="container py-4">
        <?php
        $current_tab = 'upcomings_crud';
        include '../components/admin_t_nav.php'
        ?>

        <h1 class="mb-4">Upcoming Events Management</h1>

        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- FORM -->
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <?= $edit ? "Edit Event" : "Add Event" ?>
            </div>
            <div class="card-body">

                <form method="POST" enctype="multipart/form-data">
                    <?php if ($edit): ?>
                        <input type="hidden" name="id" value="<?= $edit['id'] ?>">
                    <?php endif; ?>

                    <label>Heading</label>
                    <input type="text" name="heading" class="form-control mb-3" required
                        value="<?= htmlspecialchars($edit['heading'] ?? '') ?>">

                    <label>Content</label>
                    <textarea name="content" class="form-control mb-3" rows="4"><?= htmlspecialchars($edit['content'] ?? '') ?></textarea>

                    <label>Image Side</label>
                    <select name="image_side" class="form-select mb-3">
                        <option value="left" <?= ($edit['image_side'] ?? '') == 'left' ? 'selected' : '' ?>>Left</option>
                        <option value="right" <?= ($edit['image_side'] ?? '') == 'right' ? 'selected' : '' ?>>Right</option>
                    </select>

                    <label>Upload Image</label>
                    <input type="file" name="image" class="form-control mb-3" accept="image/*">

                    <?php if ($edit && $edit['image']): ?>
                        <img src="../assets/upcomings/<?= htmlspecialchars($edit['image']) ?>" style="height:100px" class="mb-2">
                    <?php endif; ?>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="is_active" id="isActive"
                            <?= ($edit['is_active'] ?? 0) ? "checked" : "" ?>>
                        <label class="form-check-label" for="isActive">Mark as IMPORTANT</label>
                    </div>

                    <button class="btn btn-success"><?= $edit ? "Update" : "Add" ?></button>
                    <?php if ($edit): ?>
                        <a href="upcomings_crud.php" class="btn btn-secondary">Cancel</a>
                    <?php endif; ?>
                </form>

            </div>
        </div>

        <!-- LIST ALL EVENTS -->
        <h3>All Events</h3>
        <div class="row">

            <?php foreach ($upcomings as $row): ?>

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
            <?php endif; ?>

        </div>

    </div>
</body>

</html>