<?php
session_start();
include 'funcs/connect.php';

// Fetch clubs with member counts
$clubsQuery = "SELECT c.id, c.name, c.bgimg, COUNT(mc.member_id) as member_count 
               FROM clubs c 
               LEFT JOIN member_clubs mc ON c.id = mc.club_id 
               GROUP BY c.id, c.name, c.bgimg   
               ORDER BY member_count DESC, c.name ASC
               LIMIT 3";
$clubsResult = $conn->query($clubsQuery);
$clubs = [];
while ($row = $clubsResult->fetch_assoc()) {
  $clubs[] = $row;
}

// Fetch active upcoming events for carousel
$upcomingsQuery = "SELECT heading, content, image, image_side 
                   FROM upcomings 
                   WHERE is_active = 1 
                   ORDER BY id DESC 
                   LIMIT 3";
$upcomingsResult = $conn->query($upcomingsQuery);
$carouselItems = [];
while ($row = $upcomingsResult->fetch_assoc()) {
  $carouselItems[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RPSU CLUB - Social Welfare Club</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

  <link href="./assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="./assets/bootstrap/css/bootstrap-icons.css" rel="stylesheet">
  <!-- Swiper.js CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">



  <!-- Your Root Variables -->
  <link rel="stylesheet" href="assets/css/root.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/clubsec.css">
  <link rel="stylesheet" href="assets/css/nav.css?v=<?php echo time(); ?>">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&display=swap" rel="stylesheet">

</head>

<body>

  <?php include 'components/navbar.php'; ?>

  <main>
    <section>

      <!-- HERO SLIDER - FULLY WORKING -->
      <div class="hero-slider swiper">
        <div class="swiper-wrapper">

          <!-- Slide 1 -->
          <div class="swiper-slide hero-slide">
            <img src="assets/images/herobg.jpeg" alt="RPSU CLUB Event">
            <div class="hero-content">
              <h1>Welcome to RPSU CLUB</h1>
              <p>Empowering students, transforming communities through welfare and unity</p>
              <a href="clubs.php" class="btn-hero">Explore Our Clubs</a>
            </div>
          </div>

          <!-- Slide 2 -->
          <div class="swiper-slide hero-slide">
            <img src="assets/images/herobg3.jpg" alt="Community Impact">
            <div class="hero-content">
              <h1>Be the Change</h1>
              <p>Join hands with us in creating positive impact across campus and beyond</p>
              <a href="signup.php" class="btn-hero">Join Us Today</a>
            </div>
          </div>

          <!-- Slide 3 -->
          <div class="swiper-slide hero-slide">
            <img src="assets/images/bgimg2.jpeg" alt="Leadership & Growth">
            <div class="hero-content">
              <h1>Grow Together</h1>
              <p>Develop leadership, skills, and lifelong friendships through our diverse clubs</p>
              <a href="gallery.php" class="btn-hero">View Gallery</a>
            </div>
          </div>

        </div>

        <!-- Navigation Arrows -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>

        <!-- Pagination Dots -->
        <div class="swiper-pagination"></div>
      </div>

    </section>
    <div class="void-bg" style="background-image: url(./assets/images/aceProfile.jpg);">

    </div>
    <!-- Clubs Section -->
    <section class="clubs-section py-5">
      <div class="container">
        <h1 class="section-title">Our Clubs</h1>
        <p class="text-center text-muted mb-5 lead">Discover communities that inspire growth, passion, and impact</p>

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


  </main>

  <?php include 'components/footer.php'; ?>



  <!-- Bootstrap JS Bundle with Popper -->
  <script src="./assets/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- Swiper.js JS -->

  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

  <script>
    // Initialize Swiper AFTER everything loads
    document.addEventListener('DOMContentLoaded', function() {
      new Swiper('.hero-slider', {
        loop: true,
        autoplay: {
          delay: 5000,
          disableOnInteraction: false,
          pauseOnMouseEnter: true
        },
        speed: 1000,
        effect: 'fade',
        fadeEffect: {
          crossFade: true
        },
        pagination: {
          el: '.swiper-pagination',
          clickable: true,
          dynamicBullets: true
        },
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev'
        },
        keyboard: true,
        grabCursor: true
      });
    });
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const navbar = document.querySelector('.navbar');
      let lastScrollY = window.scrollY;
      let scrollTimeout;

      function updateNavbar() {
        const currentScrollY = window.scrollY; // Get current scroll position inside the function

        if (currentScrollY === 0) {
          navbar.classList.remove('hide');
        } else if (currentScrollY < lastScrollY) {
          navbar.classList.remove('hide');
        } else if (currentScrollY > lastScrollY + 75) {
          navbar.classList.add('hide');
        }

        lastScrollY = currentScrollY; // Update lastScrollY with the current position
      }

      function handleScroll() {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(updateNavbar, 100);
      }

      // Listen to scroll events
      window.addEventListener('scroll', handleScroll, {
        passive: true
      });
      window.addEventListener('scrollend', updateNavbar, {
        passive: true
      });
      window.addEventListener('wheel', handleScroll, {
        passive: true
      });

      // Handle keyboard scrolling
      window.addEventListener('keydown', function(e) {
        if (['ArrowDown', 'ArrowUp', 'PageDown', 'PageUp', 'Space'].includes(e.key)) {
          handleScroll();
        }
      });

      // Handle touch scrolling
      let touchStartY = 0;
      window.addEventListener('touchstart', function(e) {
        touchStartY = e.touches[0].clientY;
      }, {
        passive: true
      });

      window.addEventListener('touchend', function(e) {
        const touchEndY = e.changedTouches[0].clientY;
        if (Math.abs(touchStartY - touchEndY) > 30) {
          handleScroll();
        }
      }, {
        passive: true
      });
    });
  </script>
</body>

</html>