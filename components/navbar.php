<?php
// Navigation component for frontend pages
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- 1. Variables first -->
<link rel="stylesheet" href="assets/css/root.css">

<!-- 2. Component styles -->
<link rel="stylesheet" href="assets/css/footer.css">
<link rel="stylesheet" href="assets/css/nav.css">
<!-- add more component CSS here -->

<!-- 3. Bootstrap last -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top shadow">
  <div class="container-fluid">
    <!-- Logo + Title -->
    <a class="navbar-brand" href="index.php">
      <img src="./assets/images/logo.png" alt="Logo" /> RPSU SWC
    </a>

    <!-- Hamburger -->
    <button
      class="navbar-toggler text-white"
      type="button"
      data-bs-toggle="offcanvas"
      data-bs-target="#mobileMenu">
      <span class="navbar-toggler-icon">
        <i class="bi bi-list" style="font-size: 2rem"></i>
      </span>
    </button>

    <!-- Desktop Menu -->
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 d-flex">
        <!-- About Us -->
        <li class="nav-item dropdown">
          <a
            class="nav-link dropdown-toggle"
            href="#"
            data-bs-toggle="dropdown">About Us</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="mission_vision.php">Mission & Vision</a></li>
            <li>
              <a class="dropdown-item" href="executives.php">Executive Committee</a>
            </li>
          </ul>
        </li>

        <!-- Membership -->
        <li class="nav-item dropdown">
          <a
            class="nav-link dropdown-toggle"
            href="#"
            data-bs-toggle="dropdown">Membership</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="executivi.php">Join Us</a></li>
            <li><a class="dropdown-item" href="rules.php">Benefits & Rules</a></li>
          </ul>
        </li>

        <!-- Activities -->
        <li class="nav-item dropdown">
          <a
            class="nav-link dropdown-toggle"
            href="#"
            data-bs-toggle="dropdown">Activities</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Projects</a></li>
            <li>
              <a class="dropdown-item" href="upcomings.php">Events & Announcements</a>
            </li>
            <li><a class="dropdown-item" href="gallery.php">Media Gallery</a></li>
          </ul>
        </li>

        <!-- Support Us -->
        <li class="nav-item dropdown">
          <a
            class="nav-link dropdown-toggle"
            href="#"
            data-bs-toggle="dropdown">Support Us</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Donations</a></li>
            <li><a class="dropdown-item" href="#">Sponsorships</a></li>
          </ul>
        </li>

        <!-- Clubs -->
        <li class="nav-item"><a class="nav-link" href="clubs.php">Clubs</a></li>

        <!-- Contact -->
        <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>

        <!-- Login/Account -->
        <?php if (isset($_SESSION['id'])): ?>
          <div class="dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              <img
                src="<?php echo $_SESSION['img'] ??  'assets/defaultUser.jpg' ?>"
                alt="Profile"
                width="32"
                height="32"
                class="rounded-circle">
            </a>

            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="myaccount.php">My Account</a></li>
              <li><a class="dropdown-item" href="requesting.php">Request Executive</a></li>
              <li><a class="dropdown-item" href="funcs/logout.php">Logout</a></li>
            </ul>
          </div>

        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Offcanvas Sidebar for Mobile -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="mobileMenu">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Menu</h5>
    <button
      type="button"
      class="btn-close"
      data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <div class="accordion" id="mobileAccordion">
      <!-- About Us -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="aboutHeading">
          <button
            class="accordion-button collapsed"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#aboutCollapse">
            About Us
          </button>
        </h2>
        <div
          id="aboutCollapse"
          class="accordion-collapse collapse"
          data-bs-parent="#mobileAccordion">
          <div class="accordion-body">
            <a href="mission_vision.php">Mission & Vision</a>
            <a href="executives.php">Executive Committee</a>
          </div>
        </div>
      </div>

      <!-- Membership -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="membershipHeading">
          <button
            class="accordion-button collapsed"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#membershipCollapse">
            Membership
          </button>
        </h2>
        <div
          id="membershipCollapse"
          class="accordion-collapse collapse"
          data-bs-parent="#mobileAccordion">
          <div class="accordion-body">
            <a href="signup.php">Join Us</a>
            <a href="rules.php">Benefits & Rules</a>
          </div>
        </div>
      </div>

      <!-- Activities -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="activitiesHeading">
          <button
            class="accordion-button collapsed"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#activitiesCollapse">
            Activities
          </button>
        </h2>
        <div
          id="activitiesCollapse"
          class="accordion-collapse collapse"
          data-bs-parent="#mobileAccordion">
          <div class="accordion-body">
            <a href="#">Projects</a>
            <a href="upcomings.php">Events & Announcements</a>
            <a href="gallery.php">Media Gallery</a>
          </div>
        </div>
      </div>

      <!-- Support Us -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="supportHeading">
          <button
            class="accordion-button collapsed"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#supportCollapse">
            Support Us
          </button>
        </h2>
        <div
          id="supportCollapse"
          class="accordion-collapse collapse"
          data-bs-parent="#mobileAccordion">
          <div class="accordion-body">
            <a href="#">Donations</a>
            <a href="#">Sponsorships</a>
          </div>
        </div>
      </div>

      <!-- Clubs -->
      <a class="nav-link py-2" href="clubs.php">Clubs</a>

      <!-- Contact -->
      <a class="nav-link py-2" href="#">Contact</a>

      <!-- Login/Account -->
      <?php if (isset($_SESSION['id'])): ?>
        <a class="nav-link py-2" href="myaccount.php">My Account</a>
        <a class="nav-link py-2" href="requesting.php">Request Executive</a>
        <a class="nav-link py-2" href="funcs/logout.php">Logout</a>
      <?php else: ?>
        <a class="nav-link py-2" href="login.php">Login</a>
      <?php endif; ?>
    </div>
  </div>
</div>