<?php
// Security check - must be logged in as admin
require_once __DIR__ . '/../funcs/check_admin.php';
include __DIR__ . '/../funcs/connect.php';

$uploadDir = __DIR__ . '/../assets/upcomings/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$message = "";

/* -------------------------
   DELETE
-------------------------- */
if (isset($_GET['delete'])) {
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
}

/* -------------------------
   TOGGLE IMPORTANT
-------------------------- */
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];

    // Set all inactive
    $conn->query("UPDATE upcomings SET is_active=0");

    // Set selected active
    $conn->query("UPDATE upcomings SET is_active=1 WHERE id=$id");

    header("Location: upcomings_crud.php");
    exit;
}

/* -------------------------
   LOAD EDIT DATA
-------------------------- */
$edit = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = $conn->query("SELECT * FROM upcomings WHERE id=$id");
    $edit = $res->fetch_assoc();
}

/* -------------------------
   ADD / UPDATE FORM
-------------------------- */
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $id         = $_POST['id'] ?? null;
    $heading    = $_POST['heading'];
    $content    = $_POST['content'];
    $image_side = ($_POST['image_side'] === "right") ? "right" : "left";
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
        $stmt = $conn->prepare("UPDATE upcomings SET heading=?, content=?, image=?, image_side=?, is_active=? WHERE id=?");
        $stmt->bind_param("ssssii", $heading, $content, $image, $image_side, $is_active, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO upcomings (heading, content, image, image_side, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $heading, $content, $image, $image_side, $is_active);
    }

    $stmt->execute();
    $stmt->close();

    header("Location: upcomings_crud.php");
    exit;
}
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

        <?php if ($message) echo $message; ?>

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
                        value="<?= $edit['heading'] ?? '' ?>">

                    <label>Content</label>
                    <textarea name="content" class="form-control mb-3" rows="4"><?= $edit['content'] ?? '' ?></textarea>

                    <label>Image Side</label>
                    <select name="image_side" class="form-select mb-3">
                        <option value="left" <?= ($edit['image_side'] ?? '') == 'left' ? 'selected' : '' ?>>Left</option>
                        <option value="right" <?= ($edit['image_side'] ?? '') == 'right' ? 'selected' : '' ?>>Right</option>
                    </select>

                    <label>Upload Image</label>
                    <input type="file" name="image" class="form-control mb-3">

                    <?php if ($edit && $edit['image']): ?>
                        <img src="../assets/upcomings/<?= $edit['image'] ?>" style="height:100px" class="mb-2">
                    <?php endif; ?>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="is_active"
                            <?= ($edit['is_active'] ?? 0) ? "checked" : "" ?>>
                        <label class="form-check-label">Mark as IMPORTANT</label>
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

            <?php
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

            <?php endwhile; ?>

        </div>

    </div>
</body>

</html>