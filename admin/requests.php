<?php
// Security check - must be logged in as admin
require_once __DIR__ . '/../funcs/check_admin.php';
include __DIR__ . '/../funcs/connect.php';

/* ------------------------------
   APPROVE / UNAPPROVE TOGGLE
--------------------------------*/
if (isset($_GET['toggle_approve'])) {
    $id = (int)$_GET['toggle_approve'];
    $conn->query("UPDATE executives SET approved = NOT approved WHERE id = $id");
    header("Location: requests.php");
    exit;
}

/* ------------------------------
   FETCH EXECUTIVES
--------------------------------*/
$sql = "
SELECT e.id, e.name, e.email, e.approved,
       c.name AS club_name,
       p.position_name
FROM executives e
LEFT JOIN clubs c ON e.club_id = c.id
LEFT JOIN positions p ON e.position_id = p.id
ORDER BY e.approved ASC, e.id DESC
";

$executives = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Executive Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link href="../assets/css/admidMembers.css" rel="stylesheet">
    <style>
        .badge-pending {
            background-color: #6c757d;
        }

        .badge-approved {
            background-color: #198754;
        }
        tbody :nth-child(2n) td {
            background-color: #e9e9e9;
        }
    </style>
</head>

<body class="bg-light">
    <?php
    $current_tab = 'requests';
    include '../components/admin_t_nav.php'
    ?>
    <div class="container mt-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Executive Members</h2>

            <!-- TOGGLE BUTTON -->
            <button id="toggleApproved" class="btn btn-outline-primary btn-lg shadow-sm">
                <i class="bi bi-eye-slash" id="toggleIcon"></i>
                <span id="toggleText">Show approved</span>
            </button>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Club</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $executives->fetch_assoc()): ?>
                            <tr class="<?= !$row['approved'] ? '' : 'unAproved-row  table-warning' ?>">
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= $row['club_name'] ? htmlspecialchars($row['club_name']) : '<em class="text-muted">N/A</em>' ?></td>
                                <td><?= $row['position_name'] ? htmlspecialchars($row['position_name']) : '<em class="text-muted">N/A</em>' ?></td>
                                <td>
                                    <?php if ($row['approved']): ?>
                                        <span class="badge badge-approved rounded-pill">Approved</span>
                                    <?php else: ?>
                                        <span class="badge badge-pending rounded-pill">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?toggle_approve=<?= $row['id'] ?>"
                                        class="btn btn-sm <?= $row['approved'] ? 'btn-outline-danger' : 'btn-outline-success' ?>">
                                        <?= $row['approved'] ? 'Unapprove' : 'Approve' ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if ($executives->num_rows == 0): ?>
            <div class="alert alert-info mt-4">No executives found.</div>
        <?php endif; ?>

    </div>

    <!-- Toggle Script -->
    <script>
        document.getElementById('toggleApproved').addEventListener('click', function() {
            const unAproved = document.querySelectorAll('.unAproved-row');
            const icon = document.getElementById('toggleIcon');
            const text = document.getElementById('toggleText');

            unAproved.forEach(row => {
                row.style.display = row.style.display === 'none' ? '' : 'none';
            });

            // Toggle button appearance
            if (unAproved[0]?.style.display === 'none') {
                icon.classList.replace('bi-eye-slash', 'bi-eye');
                text.textContent = 'Show approved';
                this.classList.replace('btn-outline-primary', 'btn-outline-secondary');
            } else {
                icon.classList.replace('bi-eye', 'bi-eye-slash');
                text.textContent = 'Hide approved';
                this.classList.replace('btn-outline-secondary', 'btn-outline-primary');
            }
        });
    </script>

</body>

</html>