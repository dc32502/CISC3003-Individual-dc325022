<?php
$dBServername = "localhost";
$dBUsername = "root";
$dBPassword = "";
$dBName = "b_5";

// Create connection
$conn = mysqli_connect($dBServername, $dBUsername, $dBPassword, $dBName);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
