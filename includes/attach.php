<!-- 1️⃣ attach.php (Database connection) -->
<?php
// attach.php
$servername = "localhost";
$username = "root";
$password = ""; // XAMPP default password
$dbname = "online-book-store"; // updated DB name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}else{
    // echo "connection succesfull.";
}
?>
