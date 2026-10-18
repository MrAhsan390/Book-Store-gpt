admin/dashboard.php – Admin Dashboard
<?php
session_start();
include("../includes/attach.php");
include("../includes/header.php");

// Check admin session (you can also use a role column in users table)
if(!isset($_SESSION['userid']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../login.php");
    exit;
}

// Fetch counts for summary
$totalBooks = $conn->query("SELECT COUNT(*) as cnt FROM books")->fetch_assoc()['cnt'];
$totalUsers = $conn->query("SELECT COUNT(*) as cnt FROM users")->fetch_assoc()['cnt'];
$totalOrders = $conn->query("SELECT COUNT(*) as cnt FROM orders")->fetch_assoc()['cnt'];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Dashboard - Online Bookstore</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f4f4;
    margin:0;
    padding:0;
}
.container {
    max-width: 1200px;
    margin: 30px auto;
    padding: 20px;
}
h1 {
    color: #2b6cb0;
    text-align: center;
    margin-bottom: 30px;
}
.cards {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}
.card {
    background: #fff;
    flex: 1 1 200px;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.card h2 {
    font-size: 36px;
    color: #2b6cb0;
    margin: 10px 0;
}
.card p {
    margin: 5px 0;
    font-size: 16px;
}
.card a {
    display: inline-block;
    margin-top: 10px;
    padding: 8px 12px;
    background: #2b6cb0;
    color: #fff;
    border-radius: 5px;
    text-decoration: none;
}
.card a:hover {
    background: #1e4f8a;
}
</style>
</head>
<body>

<div class="container">
<h1>Admin Dashboard</h1>
<div class="cards">
    <div class="card">
        <h2><?= $totalBooks ?></h2>
        <p>Total Books</p>
        <a href="booksShow.php">Manage Books</a>
    </div>
    <div class="card">
        <h2><?= $totalUsers ?></h2>
        <p>Total Users</p>
        <a href="users.php">Manage Users</a>
    </div>
    <div class="card">
        <h2><?= $totalOrders ?></h2>
        <p>Total Orders</p>
        <a href="orders.php">View Orders</a>
    </div>
</div>
</div>

<?php include("../includes/footer.php"); ?>
</body>
</html>
