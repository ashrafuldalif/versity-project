<?php
session_start();
include 'funcs/connect.php';

// Fetch clubs with member counts
$clubsQuery = "SELECT c.id, c.name, COUNT(mc.member_id) as member_count 
               FROM clubs c 
               LEFT JOIN member_clubs mc ON c.id = mc.club_id 
               GROUP BY c.id, c.name 
               ORDER BY member_count DESC, c.name ASC";
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
  <title>RPSU SWC - Social Welfare Club</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

  <!-- Swiper.js CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

  <!-- Your Root Variables -->
  <link rel="stylesheet" href="assets/css/root.css">
  <link rel="stylesheet" href="assets/css/clubsec.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&display=swap" rel="stylesheet">
  <style>
    /* ========================================= */
    /* HERO SLIDER – 100% USING :root VARIABLES  */
    /* ========================================= */
    @import url('root.css');
    /* If you keep variables in separate file */

    /* Full page flex layout (must be in your global CSS) */
    html,
    body {
      height: 100%;
      margin: 0;
    }

    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    main {
      flex: 1;
    }

    /* Hero Container */
    .hero-slider {
      height: 100vh;
      min-height: 500px;
      position: relative;
      overflow: hidden;
    }

    /* Each Slide */
    .hero-slide {
      position: relative;
    }

    .hero-slide::before {
      content: '';
      position: absolute;
      inset: 0;
      background: var(--overlay-dark);
      z-index: 1;
    }

    .hero-slide img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    /* Text Content */
    .hero-content {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 10;
      text-align: center;
      color: var(--text-on-dark);
      width: 90%;
      max-width: 1000px;
      padding: 2rem;
    }

    .hero-content h1 {
      font-size: clamp(2.5rem, 8vw, 4.5rem);
      font-weight: 900;
      color: var(--accent-color);
      text-shadow: 0 4px 20px rgba(0, 0, 0, 0.7);
      margin-bottom: 1.5rem;
      line-height: 1.1;
    }

    .hero-content p {
      font-size: clamp(1rem, 3vw, 1.4rem);
      margin-bottom: 2rem;
      padding: 1rem 1.5rem;
      border-radius: 8px;
      color: #fff;
      opacity: 0.95;
      max-width: 700px;
      margin-left: auto;
      margin-right: auto;
      font-weight: 700s;
    }

    /* CTA Button – fully using root vars */
    .btn-hero {
      background: var(--accent-color);
      color: var(--text-dark);
      padding: 1.1rem 3rem;
      font-size: 1.3rem;
      font-weight: 700;
      border-radius: 50px;
      border: none;
      transition: var(--transition, all 0.4s ease);
      box-shadow: 0 10px 30px rgba(217, 131, 36, 0.4);
      /* fallback if no variable */
      text-decoration: none;
      display: inline-block;
      cursor: pointer;
    }

    .btn-hero:hover {
      background: var(--secondary-hover, #e69538);
      transform: translateY(-6px);
      box-shadow: 0 18px 40px rgba(217, 131, 36, 0.6);
      color: var(--text-dark);
    }

    /* Swiper Navigation Arrows – 100% root variables */
    .swiper-button-next,
    .swiper-button-prev {
      width: 60px !important;
      height: 60px !important;
      background: rgba(255, 255, 255, 0.25);
      border-radius: 50%;
      backdrop-filter: blur(12px);
      color: var(--accent-color) !important;
      z-index: 20 !important;
      transition: var(--transition, all 0.4s ease);
      margin-top: -30px !important;
    }

    .swiper-button-next:hover,
    .swiper-button-prev:hover {
      background: var(--accent-color);
      color: white !important;
      transform: scale(1.15);
    }

    .swiper-button-next::after,
    .swiper-button-prev::after {
      font-size: 2rem !important;
      font-weight: bold !important;
    }

    /* Pagination Bullets */
    .swiper-pagination {
      bottom: 30px !important;
      z-index: 15 !important;
    }

    .swiper-pagination-bullet {
      width: 14px;
      height: 14px;
      background: rgba(255, 255, 255, 0.6);
      opacity: 1;
      transition: all 0.3s;
    }

    .swiper-pagination-bullet-active {
      background: var(--accent-color);
      transform: scale(1.4);
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
      .btn-hero {
        padding: 0.9rem 2.2rem;
        font-size: 1.1rem;
      }

      .swiper-button-next,
      .swiper-button-prev {
        width: 50px !important;
        height: 50px !important;
      }

      .swiper-button-next::after,
      .swiper-button-prev::after {
        font-size: 1.6rem !important;
      }
    }
  </style>
</head>

<body>

  <?php include 'components/navbar.php'; ?>

  <main>

    <!-- HERO SLIDER - FULLY WORKING -->
    <div class="hero-slider swiper">
      <div class="swiper-wrapper">

        <!-- Slide 1 -->
        <div class="swiper-slide hero-slide">
          <img src="assets/images/aceProfile.jpg" alt="RPSU SWC Event">
          <div class="hero-content">
            <h1>Welcome to RPSU SWC</h1>
            <p>Empowering students, transforming communities through welfare and unity</p>
            <a href="clubs.php" class="btn-hero">Explore Our Clubs</a>
          </div>
        </div>

        <!-- Slide 2 -->
        <div class="swiper-slide hero-slide">
          <img src="assets/images/choperProfile.jpg" alt="Community Impact">
          <div class="hero-content">
            <h1>Be the Change</h1>
            <p>Join hands with us in creating positive impact across campus and beyond</p>
            <a href="signup.php" class="btn-hero">Join Us Today</a>
          </div>
        </div>

        <!-- Slide 3 -->
        <div class="swiper-slide hero-slide">
          <img src="assets/images/boaProfile.jpg" alt="Leadership & Growth">
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
                      <img src="./assets/images/rpsubg.jpeg"
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

  <!-- Scripts at the end -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
</body>

</html>