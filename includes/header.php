<?php
// includes/header.php (v3 – final)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Book Store</title>
</head>
<body>



<header style="background:#222; color:#fff; padding:15px;">
    <nav style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <a href="/book--store/index.php" style="color:#fff; text-decoration:none; font-weight:bold; font-size:18px;">
                📚 Online Bookstore
            </a>
        </div>
        <div>
            <a href="./books/booksShowUserMode.php" style="color:#fff; margin:0 10px;">Books</a>
            <a href="./cart/viewCart.php" style="color:#fff; margin:0 10px;">
                Cart (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)
            </a>
            <?php if (isset($_SESSION['username'])): ?>
                <a href="./profile.php" style="color:#fff; margin:0 10px;">Profile</a>
                <a href="./logout.php" style="color:#fff; margin:0 10px;">Logout</a>
            <?php else: ?>
                <a href="./login.php" style="color:#fff; margin:0 10px;">Login</a>
                <a href="./register.php" style="color:#fff; margin:0 10px;">Register</a>
            <?php endif; ?>
        </div>
    </nav>
</header>

