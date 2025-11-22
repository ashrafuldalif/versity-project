<?php
session_start();
include '../funcs/connect.php';

if (!isset($_SESSION['id'])) {
    echo 'error';
    exit;
}
$id = $_SESSION['id'];

$name   = trim($_POST['name'] ?? '');
$batch  = trim($_POST['batch'] ?? '');
$mail   = trim($_POST['mail'] ?? '');
$phone  = trim($_POST['phone'] ?? '');
$blood  = trim($_POST['blood'] ?? '');
$pass   = $_POST['pass'] ?? '';

$bio = trim($_POST['bio'] ?? '');
$facebook = trim($_POST['facebook'] ?? '');
$instagram = trim($_POST['instagram'] ?? '');
$linkedin = trim($_POST['linkedin'] ?? '');
$x = trim($_POST['x'] ?? '');
$youtube = trim($_POST['youtube'] ?? '');

// Basic validation
if (!$name || !$batch || !$mail || !$phone || !$blood) {
    echo 'missing fields';
    exit;
}

// Build UPDATE
$sets = [];
$params = [];
$types  = '';

if ($name !== '') {
    $sets[] = "name = ?";
    $params[] = $name;
    $types .= 's';
}
if ($batch !== '') {
    $sets[] = "batch = ?";
    $params[] = $batch;
    $types .= 'i';
}
if ($mail !== '') {
    $sets[] = "mail = ?";
    $params[] = $mail;
    $types .= 's';
}
if ($phone !== '') {
    $sets[] = "phone = ?";
    $params[] = $phone;
    $types .= 's';
}
if ($blood !== '') {
    $sets[] = "bloodGroup = ?";
    $params[] = $blood;
    $types .= 's';
}
if ($pass !== '') {
    $sets[] = "pass = ?";
    $params[] = password_hash($pass, PASSWORD_DEFAULT);
    $types .= 's';
}

if (empty($sets)) {
    echo 'nothing to update';
    exit;
}

$sql = "UPDATE club_members SET " . implode(', ', $sets) . " WHERE id = ?";
$params[] = $id;
$types   .= 's';

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
// Execute club_members update
$ok = $stmt->execute() ? true : false;
$stmt->close();

if (!$ok) {
    echo 'db error';
    exit;
}

// If user is an executive, update executive-specific fields
$execCheck = $conn->prepare("SELECT 1 FROM executives WHERE id = ? LIMIT 1");
if ($execCheck) {
    $execCheck->bind_param('s', $id);
    $execCheck->execute();
    $er = $execCheck->get_result();
    if ($er && $er->fetch_assoc()) {
        // update bio if provided
        if ($bio !== '') {
            $u = $conn->prepare("UPDATE executives SET bio = ? WHERE id = ?");
            if ($u) {
                $u->bind_param('ss', $bio, $id);
                $u->execute();
                $u->close();
            }
        }

        // upsert socials into executive_socials
        if ($facebook !== '' || $instagram !== '' || $linkedin !== '' || $x !== '' || $youtube !== '') {
            // use INSERT ... ON DUPLICATE KEY UPDATE (executive_id is PRIMARY KEY)
            $ins = $conn->prepare("INSERT INTO executive_socials (executive_id, facebook, instagram, linkedin, x, youtube) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE facebook = VALUES(facebook), instagram = VALUES(instagram), linkedin = VALUES(linkedin), x = VALUES(x), youtube = VALUES(youtube)");
            if ($ins) {
                $ins->bind_param('ssssss', $id, $facebook, $instagram, $linkedin, $x, $youtube);
                $ins->execute();
                $ins->close();
            }
        }
    }
    $execCheck->close();
}

echo 'success';
