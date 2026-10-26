
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$baseUrl = '/project/BOOK-STORE-GPT';
$cartCount = 0;

if (isset($_SESSION['userid']) && isset($conn)) {
    $stmt = $conn->prepare(
        'SELECT COALESCE(SUM(quantity), 0) AS item_count
         FROM cart
         WHERE user_id = ?'
    );

    $stmt->bind_param('i', $_SESSION['userid']);
    $stmt->execute();

    $cartCount = (int) $stmt->get_result()
        ->fetch_assoc()['item_count'];

    $stmt->close();
}
?>

<header style="background:#222; color:#fff; padding:15px;">
    <nav style="display:flex; justify-content:space-between;
                align-items:center; flex-wrap:wrap; gap:10px;">

        <div>
            <a href="<?= $baseUrl ?>/index.php"
               style="color:#fff; text-decoration:none;
                      font-weight:bold; font-size:18px;">
                📚 Online Bookstore
            </a>
        </div>

        <div>
            <a href="<?= $baseUrl ?>/books/booksShowUserMode.php"
               style="color:#fff; margin:0 10px;">Books</a>

            <a href="<?= $baseUrl ?>/cart/viewCart.php"
               style="color:#fff; margin:0 10px;">
                Cart (<?= $cartCount ?>)
            </a>

            <?php if (isset($_SESSION['username'])): ?>
                <a href="<?= $baseUrl ?>/profile.php"
                   style="color:#fff; margin:0 10px;">
                    <?= htmlspecialchars($_SESSION['username']) ?>
                </a>

                <a href="<?= $baseUrl ?>/logout.php"
                   style="color:#fff; margin:0 10px;">Logout</a>
            <?php else: ?>
                <a href="<?= $baseUrl ?>/login.php"
                   style="color:#fff; margin:0 10px;">Login</a>

                <a href="<?= $baseUrl ?>/register.php"
                   style="color:#fff; margin:0 10px;">Register</a>
            <?php endif; ?>
        </div>
    </nav>
</header>
