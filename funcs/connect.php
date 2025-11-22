<?php
try {
    $conn = mysqli_connect("localhost", "root", "", "rpsu_clubs");
} catch (Exception $e) {
    echo "not connected";
}
