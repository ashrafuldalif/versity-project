<?php
include 'funcs/connect.php';
    session_start();
    $id= $_SESSION['id'];
if (isset($id)) {
    
    $sql = "SELECT img FROM cseclubmembers WHERE studentID = $id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<h3>Uploaded Image:</h3>";
        echo "<img src='" . $row['img'] . "' width='300'>";
    } else {
        echo "Image not found.";
    }
} else {
    echo "No image ID provided.";
}
?>
