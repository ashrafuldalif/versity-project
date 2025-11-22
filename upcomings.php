<?php
session_start();
include 'funcs/connect.php';

// Fetch active upcoming events
$upcomingsQuery = "SELECT id, heading, content, image, image_side 
                  FROM upcomings 
                  WHERE is_active = 1 
                  ORDER BY id DESC";
$upcomingsResult = $conn->query($upcomingsQuery);
$upcomings = [];
while ($row = $upcomingsResult->fetch_assoc()) {
    $upcomings[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Events & Announcements - RPSU SWC</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/root.css">
  <style>
    body {
      margin-top: 76px;
      background: var(--background-light);
      overflow-x: hidden;
      position: relative;
    }
    .event-card {
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }
    .event-card:hover {
      transform: translateY(-5px);
    }
    .event-image {
      height: 300px;
      object-fit: cover;
    }
  </style>
</head>
<body>
  <?php include 'components/navbar.php'; ?>

  <div class="container py-5">
    <h1 class="text-center mb-5">Events & Announcements</h1>
    
    <?php if (!empty($upcomings)): ?>
      <?php foreach ($upcomings as $event): ?>
        <div class="card event-card mb-4">
          <div class="row g-0">
            <?php if ($event['image']): ?>
              <div class="col-md-4 <?php echo $event['image_side'] === 'right' ? 'order-md-2' : ''; ?>">
                <img src="assets/upcomings/<?php echo htmlspecialchars($event['image']); ?>" 
                     class="img-fluid event-image w-100 h-100" 
                     alt="<?php echo htmlspecialchars($event['heading']); ?>">
              </div>
            <?php endif; ?>
            <div class="col-md-<?php echo $event['image'] ? '8' : '12'; ?>">
              <div class="card-body p-4">
                <h2 class="card-title mb-3"><?php echo htmlspecialchars($event['heading']); ?></h2>
                <div class="card-text">
                  <?php echo nl2br(htmlspecialchars($event['content'])); ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="alert alert-info text-center">
        <h4>No upcoming events</h4>
        <p>Check back later for new events and announcements!</p>
      </div>
    <?php endif; ?>
  </div>

  <?php include 'components/footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

