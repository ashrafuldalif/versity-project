<?php
session_start();
include 'funcs/connect.php';

// Fetch all active and approved executives
$execQuery = "SELECT e.id, e.name, e.email, e.phone, e.department, e.batch, e.img, e.bio, e.blood_group,
                     p.position_name, p.short_form,
                     c.name as club_name,
                     es.facebook, es.instagram, es.linkedin, es.x, es.youtube
              FROM executives e
              LEFT JOIN positions p ON e.position_id = p.id
              LEFT JOIN clubs c ON e.club_id = c.id
              LEFT JOIN executive_socials es ON e.id = es.executive_id
              WHERE e.active = 1 AND e.approved = 1
              ORDER BY COALESCE(p.id, 999), e.name ASC";
$execResult = $conn->query($execQuery);

// Group executives by position
$executivesByPosition = [];
while ($row = $execResult->fetch_assoc()) {
    $position = $row['position_name'] ?? 'Executive Member';
    if (!isset($executivesByPosition[$position])) {
        $executivesByPosition[$position] = [];
    }
    $executivesByPosition[$position][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Executive Committee - RPSU SWC</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="assets/css/root.css">
  <style>
    body {
      background-color: var(--background-light);
      overflow-x: hidden;
    }
    .header-image {
      background: linear-gradient(var(--overlay-dark), var(--overlay-dark)), 
                  url('assets/images/MIKASA.jpg') center/cover no-repeat;
      height: 250px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      color: var(--text-on-dark);
      margin-top: 76px;
      position: relative;
    }
    .card-custom {
      border: none;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(68, 54, 39, 0.15);
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      background-color: var(--background-light);
    }
    .card-custom:hover {
      transform: translateY(-6px);
      box-shadow: 0 6px 16px rgba(68, 54, 39, 0.25);
    }
    .card-custom img {
      height: 280px;
      object-fit: cover;
    }
    .card-title {
      color: var(--primary-color);
    }
  </style>
</head>
<body>
  <?php include 'components/navbar.php'; ?>

  <header class="header-image">
    <h1 class="fw-bold">Executive Committee</h1>
    <p>Leading with vision and dedication</p>
  </header>

  <div class="container my-5">
    <?php foreach ($executivesByPosition as $position => $executives): ?>
      <section class="my-5">
        <h2 class="mb-3 text-center"><?php echo htmlspecialchars($position); ?></h2>
        <p class="text-muted text-center mb-4"><?php echo count($executives); ?> member(s)</p>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
          <?php foreach ($executives as $exec): ?>
            <div class="col">
              <div class="card card-custom">
                <img src="<?php echo $exec['img'] ? 'assets/members/' . htmlspecialchars($exec['img']) : 'assets/images/default.jpg'; ?>" 
                     class="card-img-top" alt="<?php echo htmlspecialchars($exec['name']); ?>">
                <div class="card-body">
                  <h5 class="card-title"><?php echo htmlspecialchars($exec['name']); ?></h5>
                  <p class="card-text">
                    <?php echo htmlspecialchars($exec['position_name'] ?? 'Executive Member'); ?><br>
                    <?php echo htmlspecialchars($exec['department']); ?><br>
                    Batch: <?php echo htmlspecialchars($exec['batch']); ?>
                    <?php if ($exec['club_name']): ?>
                      <br>Club: <?php echo htmlspecialchars($exec['club_name']); ?>
                    <?php endif; ?>
                  </p>
                  <?php if ($exec['bio']): ?>
                    <p class="card-text small text-muted"><?php echo htmlspecialchars(substr($exec['bio'], 0, 100)); ?>...</p>
                  <?php endif; ?>
                  <div class="d-flex justify-content-center gap-3 mt-3">
                    <?php if ($exec['email']): ?>
                      <a href="mailto:<?php echo htmlspecialchars($exec['email']); ?>" class="text-primary">
                        <i class="bi bi-envelope"></i>
                      </a>
                    <?php endif; ?>
                    <?php if ($exec['phone']): ?>
                      <a href="tel:<?php echo htmlspecialchars($exec['phone']); ?>" class="text-success">
                        <i class="bi bi-telephone"></i>
                      </a>
                    <?php endif; ?>
                    <?php if ($exec['facebook']): ?>
                      <a href="<?php echo htmlspecialchars($exec['facebook']); ?>" target="_blank" class="text-primary">
                        <i class="bi bi-facebook"></i>
                      </a>
                    <?php endif; ?>
                    <?php if ($exec['instagram']): ?>
                      <a href="<?php echo htmlspecialchars($exec['instagram']); ?>" target="_blank" class="text-danger">
                        <i class="bi bi-instagram"></i>
                      </a>
                    <?php endif; ?>
                    <?php if ($exec['linkedin']): ?>
                      <a href="<?php echo htmlspecialchars($exec['linkedin']); ?>" target="_blank" class="text-info">
                        <i class="bi bi-linkedin"></i>
                      </a>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; ?>

    <?php if (empty($executivesByPosition)): ?>
      <div class="alert alert-info text-center">
        <h4>No executives found</h4>
        <p>Executive committee information will be available soon.</p>
      </div>
    <?php endif; ?>
  </div>

  <?php include 'components/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

