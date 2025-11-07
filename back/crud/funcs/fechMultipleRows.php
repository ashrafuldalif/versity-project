<?php
$conn = new mysqli("localhost", "root", "", "your_database");

$stmt = $conn->prepare("SELECT id, name, email FROM users");
$stmt->execute();
$result = $stmt->get_result();

$users = []; // array to store rows

while ($row = $result->fetch_assoc()) {
  $users[] = $row;
}

$stmt->close();
$conn->close();

// Example: loop later
foreach ($users as $user) {
  echo $user['name'] . " - " . $user['email'] . "<br>";
}
?>
