<?php
session_start();
include 'components/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Benefits & Rules - RPSU SWC</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="assets/css/root.css">
  <style>
    body {
      background-color: var(--background-light);
      overflow-x: hidden;
      position: relative;
      color: var(--text-primary);
    }

    .header-image {
      background: linear-gradient(to right, var(--primary-color), var(--accent-color));
      height: 240px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--text-on-dark);
      text-align: center;
      padding: 20px;
      margin-top: 76px;
      position: relative;
    }

    .card-custom {
      border: none;
      border-radius: 12px;
      transition: all 0.3s ease;
      box-shadow: 0 2px 8px rgba(68, 54, 39, 0.1);
      background-color: var(--background-card);
    }

    .card-custom:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 16px rgba(68, 54, 39, 0.2);
    }

    .card-custom i {
      color: var(--accent-color);
    }

    .card-custom .card-body {
      color: var(--text-primary);
    }

    .card-custom .card-title {
      color: var(--text-primary);
      font-weight: 600;
    }

    .card-custom .card-text {
      color: var(--text-secondary);
    }

    .text-muted {
      color: var(--text-muted) !important;
    }

    h2,
    h5 {
      color: var(--text-primary);
    }

    .card.shadow-sm {
      background-color: var(--background-card);
      color: var(--text-primary);
    }

    .list-group-item {
      background-color: transparent;
      color: var(--text-secondary);
      border-color: var(--glass-border);
    }
  </style>
</head>

<body>
  <header class="header-image">
    <div>
      <h1 class="fw-bold mb-3">R.P. Shaha University Varsity Club</h1>
      <p>Discover the benefits of joining our club and understand the values that help us maintain a thriving, respectful community.</p>
    </div>
  </header>

  <section class="container my-5">
    <h2 class="text-center mb-3">Why Join Our Club?</h2>
    <p class="text-center mb-5 text-muted">
      Membership in our varsity club offers countless opportunities for personal growth, skill enhancement, and meaningful connections.
    </p>
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <div class="col">
        <div class="card card-custom">
          <div class="card-body">
            <i class="bi bi-people fs-2"></i>
            <h5 class="card-title mt-3">Networking Opportunities</h5>
            <p class="card-text">Connect with like-minded students, build lasting friendships, and expand your professional network.</p>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card card-custom">
          <div class="card-body">
            <i class="bi bi-tools fs-2"></i>
            <h5 class="card-title mt-3">Skill Development</h5>
            <p class="card-text">Join workshops, seminars, and training sessions to sharpen leadership, communication, and technical skills.</p>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card card-custom">
          <div class="card-body">
            <i class="bi bi-calendar-event fs-2"></i>
            <h5 class="card-title mt-3">Exclusive Events</h5>
            <p class="card-text">Enjoy priority access to events, social gatherings, cultural programs, and university celebrations.</p>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card card-custom">
          <div class="card-body">
            <i class="bi bi-trophy fs-2"></i>
            <h5 class="card-title mt-3">Competition & Recognition</h5>
            <p class="card-text">Represent the club in inter-university competitions and earn recognition for your achievements.</p>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card card-custom">
          <div class="card-body">
            <i class="bi bi-hand-thumbs-up fs-2"></i>
            <h5 class="card-title mt-3">Mentor Support</h5>
            <p class="card-text">Get guidance, tutoring, and shared resources from experienced members and faculty advisors.</p>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card card-custom">
          <div class="card-body">
            <i class="bi bi-lightbulb fs-2"></i>
            <h5 class="card-title mt-3">Leadership Experience</h5>
            <p class="card-text">Take on leadership roles, organize events, and develop real-world project management experience.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="container mb-5">
    <h2 class="text-center mb-3">Club Regulations</h2>
    <p class="text-center mb-4 text-muted">To keep our club positive and productive, members must follow these key guidelines.</p>
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title">Membership Requirements</h5>
        <ul class="list-group list-group-flush mb-4">
          <li class="list-group-item">Must be a currently enrolled student at R.P. Shaha University</li>
          <li class="list-group-item">Maintain a minimum GPA of 2.5</li>
          <li class="list-group-item">Submit the membership registration form with accurate details</li>
          <li class="list-group-item">Pay annual fees before the deadline</li>
          <li class="list-group-item">Attend the mandatory new-member orientation session</li>
        </ul>
        <h5 class="card-title mt-4">Attendance & Participation</h5>
        <ul class="list-group list-group-flush mb-4">
          <li class="list-group-item">Active participation in at least two events each semester</li>
          <li class="list-group-item">Notify the club in advance of absences from important meetings</li>
          <li class="list-group-item">Support teamwork and collaboration in all club activities</li>
        </ul>
        <h5 class="card-title mt-4">Code of Conduct</h5>
        <ul class="list-group list-group-flush mb-4">
          <li class="list-group-item">Treat all members with respect and inclusivity</li>
          <li class="list-group-item">No discrimination, harassment, or misconduct of any kind</li>
          <li class="list-group-item">Represent the club positively both on and off campus</li>
        </ul>
        <h5 class="card-title mt-4">Academic Integrity</h5>
        <ul class="list-group list-group-flush mb-4">
          <li class="list-group-item">Members must uphold honesty in academic and club-related work</li>
          <li class="list-group-item">Plagiarism or cheating will not be tolerated</li>
        </ul>
        <h5 class="card-title mt-4">Financial Responsibility</h5>
        <ul class="list-group list-group-flush mb-4">
          <li class="list-group-item">Use club funds transparently for approved activities only</li>
          <li class="list-group-item">All financial transactions must be documented and reported</li>
        </ul>
        <h5 class="card-title mt-4">Violation Consequences</h5>
        <ul class="list-group list-group-flush">
          <li class="list-group-item">1st violation: Written warning from the Executive Committee</li>
          <li class="list-group-item">2nd violation: Suspension from club activities for one month</li>
          <li class="list-group-item">3rd violation: Termination of membership without refund</li>
          <li class="list-group-item">Serious violations may lead to immediate termination</li>
          <li class="list-group-item">Appeals may be submitted to the Executive Committee within 7 days</li>
        </ul>
      </div>
    </div>
  </section>

  <?php include 'components/footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>