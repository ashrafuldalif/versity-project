<?php
include 'funcs/connect.php';
session_start();

/* ---------- 1. AUTHENTICATION ---------- */
if (!isset($_SESSION['id'])) {
  header('Location: login.php');
  exit();
}
$currentId = $_SESSION['id'];

/* ---------- 2. FETCH USER DATA ---------- */
$sql = "SELECT img, name, batch, department, mail, pass, phone, bloodGroup 
        FROM club_members 
        WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $currentId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if (!$row) {
  // Should never happen, but safe-guard
  session_destroy();
  header('Location: login.php');
  exit();
}

/* ---------- 3. EXTRACT VARIABLES ---------- */
$imgPath    = $row['img'] ? "assets/" . $row['img'] : 'assets/default.jpg';
$name       = $row['name'];
$batch      = $row['batch'];
$department = $row['department'];
$mail       = $row['mail'];
$phone      = $row['phone'];
$bloodGroup = $row['bloodGroup'];
$hashedPass = $row['pass'];   // stored hash
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Account</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f5f5f5;
      font-family: Arial, Helvetica, sans-serif;
    }

    .profile-card {
      max-width: 420px;
      margin: 60px auto;
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, .1);
      padding: 25px;
      text-align: center;
    }

    .profile-photo {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #0d6efd;
      cursor: pointer;
      margin-bottom: 15px;
    }

    #verifyContainer {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, .5);
      align-items: center;
      justify-content: center;
    }

    #verifyForm {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      width: 320px;
    }

    .hints {
      font-size: 0.9rem;
      color: #666;
      text-decoration: underline;
    }
  </style>
</head>

<body>

  <div class="profile-card">
    <img src="<?= htmlspecialchars($imgPath) ?>" id="profilePhoto" class="profile-photo" alt="Profile">
    <input type="file" id="photoInput" accept="image/*" style="display:none;">

    <div class="profile-info text-start mt-3">
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" disabled>
      </div>
      <div class="mb-3">
        <label class="form-label">Batch</label>
        <input type="number" name="batch" class="form-control" value="<?= $batch ?>" disabled>
      </div>
      <div class="mb-3">
        <label class="form-label">Department</label>
        <input type="text" name="dept" class="form-control" value="<?= htmlspecialchars($department) ?>" disabled>
      </div>
      <div class="mb-3">
        <label class="form-label">Student ID</label>
        <input type="text" name="id" class="form-control" value="<?= htmlspecialchars($currentId) ?>" disabled>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="mail" class="form-control" value="<?= htmlspecialchars($mail) ?>" disabled>
      </div>
      <div class="mb-3">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($phone) ?>" disabled>
      </div>
      <div class="mb-3">
        <label class="form-label">Blood Group</label>
        <input type="text" name="blood" class="form-control" value="<?= htmlspecialchars($bloodGroup) ?>" disabled>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="pass" class="form-control" placeholder="********" disabled>
        <p class="hints">Password is hidden for security.</p>
      </div>
    </div>

    <div class="d-flex gap-2 mt-3">
      <button id="editBtn" class="btn btn-primary flex-fill">Edit</button>
      <button onclick="window.location='funcs/logout.php'" class="btn btn-danger flex-fill">Logout</button>
    </div>
  </div>

  <!-- Password verification overlay -->
  <div id="verifyContainer">
    <form id="verifyForm">
      <h5 class="text-center mb-3">Verify Current Password</h5>
      <input type="password" id="verifyPassword" class="form-control mb-3"
        placeholder="Enter password" required>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-success w-50">Verify</button>
        <button type="button" id="cancelVerify" class="btn btn-secondary w-50">Cancel</button>
      </div>
    </form>
  </div>

  <script>
    /* ---------- DOM ELEMENTS ---------- */
    const editBtn = document.getElementById('editBtn');
    const verifyContainer = document.getElementById('verifyContainer');
    const verifyForm = document.getElementById('verifyForm');
    const cancelVerify = document.getElementById('cancelVerify');
    const inputs = document.querySelectorAll('.profile-info input');
    const profilePhoto = document.getElementById('profilePhoto');
    const photoInput = document.getElementById('photoInput');

    let isEditing = false;
    let isVerified = false;

    /* ---------- HELPERS ---------- */
    function enableEditing() {
      isEditing = true;
      inputs.forEach(i => i.disabled = false);
      editBtn.textContent = 'Save';
      editBtn.classList.replace('btn-primary', 'btn-success');
    }

    function disableEditing() {
      isEditing = false;
      inputs.forEach(i => i.disabled = true);
      editBtn.textContent = 'Edit';
      editBtn.classList.replace('btn-success', 'btn-primary');
    }

    function showVerify() {
      verifyContainer.style.display = 'flex';
    }

    function hideVerify() {
      verifyContainer.style.display = 'none';
    }

    /* ---------- EDIT / SAVE ---------- */
    editBtn.addEventListener('click', async () => {
      if (!isEditing && !isVerified) {
        showVerify();
        return;
      }

      if (isEditing && isVerified) {
        const data = {
          name: inputs[0].value,
          batch: inputs[1].value,
          mail: inputs[4].value,
          phone: inputs[5].value,
          blood: inputs[6].value,
          pass: inputs[7].value // new password (empty = no change)
        };

        const form = new URLSearchParams();
        for (const k in data) form.append(k, data[k]);

        try {
          const res = await fetch('funcs/update_profile.php', {
            method: 'POST',
            body: form,
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            }
          });
          const txt = await res.text();
          if (txt.trim() === 'success') {
            disableEditing();
            isVerified = false;
            alert('Profile updated!');
          } else {
            alert('Update failed: ' + txt);
          }
        } catch (e) {
          console.error(e);
          alert('Network error');
        }
      }
    });

    /* ---------- PASSWORD VERIFICATION ---------- */
    verifyForm.addEventListener('submit', async e => {
      e.preventDefault();
      const pwd = document.getElementById('verifyPassword').value;
      const body = new URLSearchParams({
        verifypass: pwd
      });

      try {
        const res = await fetch('funcs/verify.php', {
          method: 'POST',
          body,
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          }
        });
        const txt = await res.text();
        if (txt.trim() === 'ok') {
          hideVerify();
          isVerified = true;
          enableEditing();
        } else {
          alert('Wrong password');
        }
      } catch (e) {
        console.error(e);
        alert('Verification error');
      }
    });
    cancelVerify.addEventListener('click', hideVerify);

    /* ---------- PROFILE PHOTO ---------- */
    profilePhoto.addEventListener('click', () => {
      if (isEditing) photoInput.click();
    });
    photoInput.addEventListener('change', async e => {
      const file = e.target.files[0];
      if (!file) return;
      profilePhoto.src = URL.createObjectURL(file);

      const fd = new FormData();
      fd.append('image', file);

      try {
        const res = await fetch('funcs/uploadimg.php', {
          method: 'POST',
          body: fd
        });
        const txt = await res.text();
        alert(txt.trim());
      } catch (e) {
        console.error(e);
        alert('Upload failed');
      }
    });
  </script>

  <script src="http://localhost:35729/livereload.js"></script>
</body>

</html>