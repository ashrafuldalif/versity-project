<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mission & Vision - RPSU SWC</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/root.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: var(--background-light);
      color: var(--text-dark);
      overflow-x: hidden;
      margin-top: 76px;
      position: relative;
    }
    .section-title {
      text-align: center;
      margin: 50px 0 30px;
      font-size: 2rem;
      font-weight: 700;
      color: var(--primary-color);
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .image-container {
      width: 100%;
      border-radius: 20px;
      overflow: hidden;
      margin-bottom: 40px;
      box-shadow: 0 4px 20px rgba(68, 54, 39, 0.2);
    }
    .image-container img {
      width: 100%;
      height: 380px;
      object-fit: cover;
    }
    .mission, .vision {
      display: flex;
      align-items: flex-start;
      margin: 30px 0;
    }
    .label {
      background: var(--accent-color);
      color: var(--text-dark);
      padding: 10px 25px;
      border-radius: 20px;
      margin-right: 20px;
      font-weight: 600;
      text-transform: capitalize;
      box-shadow: 0 2px 8px rgba(68, 54, 39, 0.2);
    }
    .text {
      flex: 1;
      background: var(--background-light);
      padding: 15px 20px;
      border-radius: 15px;
      box-shadow: 0 2px 6px rgba(68, 54, 39, 0.1);
      color: var(--text-dark);
    }
    .social-links {
      text-align: center;
      margin-top: 50px;
    }
    .social-links a {
      display: inline-flex;
      justify-content: center;
      align-items: center;
      width: 45px;
      height: 45px;
      border-radius: 50%;
      background: var(--accent-color);
      color: var(--text-dark);
      font-size: 1.2rem;
      margin: 0 10px;
      transition: all 0.3s ease;
    }
    .social-links a:hover {
      background: var(--secondary-hover);
      transform: translateY(-4px);
    }
  </style>
</head>
<body>
  <?php include 'components/navbar.php'; ?>
  <div class="container py-5">
    <h2 class="section-title">Mission & Vision</h2>
    <div class="image-container">
      <img src="./assets/images/boaProfile.jpg" alt="Mission & Vision">
    </div>
    <div class="mission">
      <div class="label">Mission</div>
      <div class="text">
        <p>
          Our mission is to empower students and professionals by providing a platform that encourages collaboration,
          innovation, and lifelong learning. We aim to inspire creativity, integrity, and excellence in every initiative.
        </p>
      </div>
    </div>
    <div class="vision">
      <div class="label">Vision</div>
      <div class="text">
        <p>
          We envision a world where technology and knowledge drive positive change. Our goal is to foster a culture of
          curiosity and compassion — helping individuals reach their full potential while uplifting their communities.
        </p>
      </div>
    </div>
    <div class="social-links">
      <a href="#"><i class="bi bi-facebook"></i></a>
      <a href="#"><i class="bi bi-instagram"></i></a>
      <a href="#"><i class="bi bi-twitter-x"></i></a>
    </div>
  </div>
  <?php include 'components/footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

