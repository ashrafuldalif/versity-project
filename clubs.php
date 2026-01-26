<?php
session_start();
include 'funcs/connect.php';

// Get specific club if requested
$clubId = isset($_GET['club']) ? (int)$_GET['club'] : null;
$club = null;
$executives = [];
$members = [];
if ($clubId) {
  // Fetch specific club details
  $clubStmt = $conn->prepare("SELECT id, name, bgimg FROM clubs WHERE id = ?");
  $clubStmt->bind_param('i', $clubId);
  $clubStmt->execute();
  $clubResult = $clubStmt->get_result();
  $club = $clubResult->fetch_assoc();
  $clubStmt->close();

  if ($club) {
    // Fetch executives for this club
    $execStmt = $conn->prepare("SELECT e.id, e.name, e.img, e.department, e.batch, p.position_name 
                                   FROM executives e 
                                   LEFT JOIN positions p ON e.position_id = p.id 
                                   WHERE e.club_id = ? AND e.active = 1 AND e.approved = 1
                                   ORDER BY COALESCE(p.id, 999), e.name ASC");
    $execStmt->bind_param('i', $clubId);
    $execStmt->execute();
    $execResult = $execStmt->get_result();
    while ($row = $execResult->fetch_assoc()) {
      $executives[] = $row;
    }
    $execStmt->close();

    // Fetch members for this club
    $memberStmt = $conn->prepare("SELECT cm.id, cm.name, cm.img 
                                     FROM club_members cm 
                                     JOIN member_clubs mc ON cm.id = mc.member_id 
                                     WHERE mc.club_id = ? 
                                     ORDER BY cm.name ASC");
    $memberStmt->bind_param('i', $clubId);
    $memberStmt->execute();
    $memberResult = $memberStmt->get_result();
    while ($row = $memberResult->fetch_assoc()) {
      $members[] = $row;
    }
    $memberStmt->close();
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $club ? htmlspecialchars($club['name']) : 'All Clubs'; ?> - RPSU SWC</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/root.css">
  <style>
    .members-container {
      display: flex;
      gap: 2rem;
      overflow-x: auto; /* Allow horizontal scrolling */
      scroll-behavior: smooth;
      padding: 0 2rem;

      &::-webkit-scrollbar {
        display: none;
      }

      -ms-overflow-style: none;
      scrollbar-width: none;
    }

    .members-container div {
      flex: 0 0 auto;
    }

    .arrow-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      height: 100%;
      background: var(--overlay-dark);
      border: none;
      color: var(--text-on-dark);
      font-size: 2rem;
      padding: 0.5rem 1rem;
      cursor: pointer;
      border-radius: 8px;
      transition: all 0.5s;
      z-index: 10;
      display: none;
    }

    .arrow-btn:hover {
      color: var(--accent-color);
      background: var(--primary-hover);
    }

    .left-arrow {
      left: 0px;
    }

    .right-arrow {
      right: 0px;
    }

    .members-photo {
      height: 90px;
      width: 90px;
    }

    .popup-overlay {
      position: fixed;
      inset: 0;
      background: var(--overlay-dark);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 999;
      backdrop-filter: blur(3px);
    }

    .popup-content {
      background: var(--background-light);
      border-radius: 1.5rem;
      padding: 2rem;
      max-width: 400px;
      width: 90%;
      text-align: center;
      position: relative;
      animation: popupFade 0.25s ease;
      color: var(--text-dark);
    }

    .close-btn {
      position: absolute;
      top: 12px;
      right: 20px;
      font-size: 1.8rem;
      color: var(--text-dark);
      cursor: pointer;
      font-weight: bold;
    }

    @keyframes popupFade {
      from {
        opacity: 0;
        transform: scale(0.9);
      }

      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    @media (max-width: 768px) {
      .members-container {
        flex-wrap: wrap;
        justify-content: center;
      }

      .members-container.collapsed>div:nth-of-type(n+10) {
        display: none;
      }

      .arrow-btn {
        z-index: -12;
      }
    }
  </style>

  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/nav.css">
  <link rel="stylesheet" href="assets/css/clubsec.css">



</head>

<body>
  <?php include 'components/navbar.php'; ?>

  <?php if ($club != null): ?>
    <!-- Specific Club Section -->
    <section class="club-section text-light w-100 mt-5" style="background: url('./assets/clubs/<?php echo htmlspecialchars($club['bgimg']); ?>') no-repeat center center fixed; background-size: cover; min-height: 100vh; width: 100%; margin-top: 76px; position: relative; overflow-x: hidden;">
      <div class="container-fluid py-5">
        <h2 class="text-center mb-5" style="font-family: 'Poppins', sans-serif; font-weight:600;"><?php echo htmlspecialchars(strtoupper($club['name'])); ?>_</h2>

        <div class="row d-flex align-items-center justify-content-center g-4">
          <!-- Executives -->
          <div class="col-md-5 d-flex flex-column">
            <?php if (!empty($executives)): ?>
              <?php foreach (array_slice($executives, 0, 2) as $index => $exec): ?>
                <div class="px-3 ps-0 mb-3 player-card"
                  style="background:<?php echo $index === 0 ? 'var(--accent-color)' : 'var(--primary-color)'; ?>; border-radius: 3rem; border-start-end-radius: 0; max-width: 550px; cursor:pointer;"
                  onclick="showPopup('<?php echo htmlspecialchars($exec['name']); ?>', '<?php echo htmlspecialchars($exec['position_name'] ?? 'Member'); ?>', '<?php echo $exec['img'] ? 'assets/members/' . htmlspecialchars($exec['img']) : 'assets/images/default.jpg'; ?>', '<?php echo htmlspecialchars($exec['department']); ?>', '<?php echo htmlspecialchars($exec['batch']); ?>')">
                  <div class="d-flex align-items-center">
                    <img src="<?php echo $exec['img'] ? 'assets/members/' . htmlspecialchars($exec['img']) : 'assets/images/default.jpg'; ?>"
                      class="rounded-circle me-3" width="90" height="90" alt="<?php echo htmlspecialchars($exec['name']); ?>">
                    <div class="text-start">
                      <h5 class="mb-1 fw-bold"><?php echo htmlspecialchars($exec['position_name'] ?? 'Member'); ?></h5>
                      <p class="mb-0 small"><?php echo htmlspecialchars($exec['name']); ?></p>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <div class="col-md-5 d-flex align-items-center justify-content-center justify-content-md-end">
            <img src="assets/images/mascot.png" alt="Club Mascot" style="max-height:350px; max-width:100%; object-fit:contain;">
          </div>
        </div>

        <!-- Members Section -->
        <div class="C-membersSec w-100 p-md-5 px-1 py-5 mt-5 position-relative"
          style="background:var(--overlay-dark); width:100%; overflow:hidden; position: relative;">
          <h4 class="text-center mb-4">Members</h4>
          <div class="members-container overflow-auto">
            <?php foreach ($members as $member): ?>
              <div class="text-center">
                <img src="<?php echo $member['img'] ? 'assets/members/' . htmlspecialchars($member['img']) : 'assets/images/default.jpg'; ?>"
                  class="rounded-circle mb-2 members-photo" alt="<?php echo htmlspecialchars($member['name']); ?>">
                <p class="small mb-0"><?php echo htmlspecialchars($member['name']); ?></p>
              </div>
            <?php endforeach; ?>
          </div>
          <button class="arrow-btn left-arrow">&#10094;</button>
          <button class="arrow-btn right-arrow">&#10095;</button>
          <?php if (count($members) > 10): ?>
            <div class="text-center mt-3 d-md-none">
              <button id="seeMoreBtn" class="btn btn-link text-light">See more</button>
            </div>
          <?php endif; ?>
        </div>

        <div class="mt-5 text-center">
          <a href="register.php" class="btn btn-lg text-dark fw-bold" style="background:var(--accent-color); border-radius:15px; border:none;">Join Us</a>
        </div>
      </div>
    </section>

    <!-- Popup Modal -->
    <div class="popup-overlay" id="playerPopup">
      <div class="popup-content">
        <span class="close-btn" onclick="closePopup()">&times;</span>
        <img id="popupImg" src="" width="120" height="120" class="rounded-circle mb-3" alt="">
        <h3 class="fw-bold mb-2" id="popupName"></h3>
        <p class="mb-0" id="popupRole"></p>
        <p class="small mt-2" id="popupInfo"></p>
      </div>
    </div>
  <?php else: ?>
    <!-- All Clubs List -->
    <?php
    $clubsQuery = "SELECT c.id, c.name, c.bgimg, COUNT(mc.member_id) as member_count 
               FROM clubs c 
               LEFT JOIN member_clubs mc ON c.id = mc.club_id 
               GROUP BY c.id, c.name, c.bgimg   
               ORDER BY member_count DESC, c.name ASC";
    $clubsResult = $conn->query($clubsQuery);
    $clubs = [];
    while ($row = $clubsResult->fetch_assoc()) {
      $clubs[] = $row;
    }

    ?>
    <section class="clubs-section py-5 mt-5">
      <div class="container">
        <h1 class="section-title">Our Clubs</h1>
        <p class="text-center text-muted my-5 lead ">Discover communities that inspire growth, passion, and impact</p>

        <div class="row g-4 g-xl-5 justify-content-center">
          <?php if (!empty($clubs)): ?>
            <?php foreach ($clubs as $club): ?>
              <div class="col-lg-4 col-md-6 col-sm-8">
                <a href="clubs.php?club=<?php echo $club['id']; ?>" class="club-card-link">
                  <div class="club-card">
                    <div class="club-card-img">
                      <?php $temp = './assets/clubs/' . $club['bgimg'] ?>
                      <img src="<?php echo htmlspecialchars($temp); ?>"
                        alt="<?php echo htmlspecialchars($club['name']); ?>"
                        class="img-fluid">

                    </div>
                    <div class="club-card-overlay">
                      <div class="overlay-content">
                        <h3 class="club-name"><?php echo htmlspecialchars($club['name']); ?></h3>
                        <p class="club-members">
                          <i class="bi bi-people-fill me-2"></i>
                          <?php echo $club['member_count']; ?> Members
                        </p>
                        <span class="view-club">View Club →</span>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="col-12 text-center py-5">
              <p class="text-muted fs-4">No clubs available at the moment.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <?php include 'components/footer.php'; ?>

  <script>
    const container = document.querySelector('.members-container');
    const leftArrow = document.querySelector('.left-arrow');
    const rightArrow = document.querySelector('.right-arrow');
    const membersSection = document.querySelector('.C-membersSec');

    if (container && membersSection) {
      let direction = 1;
      let scrollSpeed = 1;
      let notHovered = true;

      function autoScrollFunc() {
        if (window.innerWidth > 768 && notHovered) {
          container.scrollLeft += scrollSpeed * direction;
          if (container.scrollLeft + container.clientWidth >= container.scrollWidth) {
            direction = -1;
          } else if (container.scrollLeft <= 0) {
            direction = 1;
          }
        }
        requestAnimationFrame(autoScrollFunc);
      }
      requestAnimationFrame(autoScrollFunc);

      membersSection.addEventListener('mouseenter', () => {
        if (leftArrow) leftArrow.style.display = 'block';
        if (rightArrow) rightArrow.style.display = 'block';
        notHovered = false;
      });
      membersSection.addEventListener('mouseleave', () => {
        if (leftArrow) leftArrow.style.display = 'none';
        if (rightArrow) rightArrow.style.display = 'none';
        notHovered = true;
      });

      if (leftArrow) leftArrow.addEventListener('click', () => {
        container.scrollBy({
          left: -200,
          behavior: 'smooth'
        });
      });
      if (rightArrow) rightArrow.addEventListener('click', () => {
        container.scrollBy({
          left: 200,
          behavior: 'smooth'
        });
      });

      const seeMoreBtn = document.getElementById('seeMoreBtn');
      if (seeMoreBtn) {
        container.classList.add('collapsed');
        seeMoreBtn.addEventListener('click', () => {
          container.classList.toggle('collapsed');
          seeMoreBtn.textContent = container.classList.contains('collapsed') ? 'See more' : 'See less';
        });
      }
    }

    function showPopup(name, role, img, dept, batch) {
      document.getElementById('popupImg').src = img;
      document.getElementById('popupName').textContent = name;
      document.getElementById('popupRole').textContent = 'Role: ' + role;
      document.getElementById('popupInfo').textContent = dept + ' • Batch: ' + batch;
      document.getElementById('playerPopup').style.display = 'flex';
    }

    function closePopup() {
      document.getElementById('playerPopup').style.display = 'none';
    }

    window.addEventListener('click', (e) => {
      const popup = document.getElementById('playerPopup');
      if (e.target === popup) {
        popup.style.display = 'none';
      }
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>