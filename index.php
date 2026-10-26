
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/attach.php';

$baseUrl = '/project/BOOK-STORE-GPT';
$result = $conn->query(
    'SELECT * FROM books ORDER BY bk_id DESC LIMIT 8'
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Bookstore</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f4f4f4;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #2b6cb0;
        }

        .books-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .book-card {
            box-sizing: border-box;
            background: white;
            padding: 15px;
            border-radius: 10px;
            width: 240px;
            box-shadow: 0 4px 10px rgba(0,0,0,.1);
            text-align: center;
        }

        .book-card img {
            width: 150px;
            height: 200px;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .book-card h3 {
            font-size: 18px;
        }

        .book-card button {
            border: 0;
            border-radius: 5px;
            padding: 9px 12px;
            margin: 5px 2px;
            background: #2b6cb0;
            color: white;
            cursor: pointer;
        }

        .book-card button:hover {
            background: #1e4f8a;
        }

        .empty-books-message {
            margin: 40px auto;
            padding: 30px 20px;
            text-align: center;
            background: #f8fafc;
            border-radius: 12px;
        }

        .notice {
            text-align: center;
            color: #166534;
            font-weight: bold;
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<div class="container">
    <h1>Welcome to the Online Bookstore</h1>

    <?php if (($_GET['cart'] ?? '') === 'added'): ?>
        <p class="notice">Book added to your cart successfully.</p>
    <?php endif; ?>

    <div class="books-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($book = $result->fetch_assoc()): ?>
                <?php
                $bookId = (int) $book['bk_id'];
                $imageName = basename($book['bk_image'] ?? '');
                $imagePath = __DIR__ . '/assets/uploads/books/' . $imageName;

                $imageUrl = $baseUrl . '/assets/uploads/books/' .
                            rawurlencode($imageName);

                if ($imageName === '' || !is_file($imagePath)) {
                    $imageUrl = $baseUrl . '/assets/images/no-image.png';
                }

                $returnTo = $baseUrl . '/index.php?cart=added';
                ?>

                <div class="book-card">
                    <img
                        src="<?= htmlspecialchars($imageUrl) ?>"
                        alt="<?= htmlspecialchars($book['bk_title']) ?>"
                    >

                    <h3><?= htmlspecialchars($book['bk_title']) ?></h3>
                    <p>Author: <?= htmlspecialchars($book['bk_author']) ?></p>
                    <p>Price: $<?= number_format((float)$book['bk_price'], 2) ?></p>

                    <a href="<?= $baseUrl ?>/books/bookDetails.php?id=<?= $bookId ?>">
                        <button type="button">View Details</button>
                    </a>

                    <form method="post"
                          action="<?= $baseUrl ?>/cart/addToCart.php">
                        <input type="hidden" name="bk_id"
                               value="<?= $bookId ?>">
                        <input type="hidden" name="return_to"
                               value="<?= htmlspecialchars($returnTo) ?>">
                        <button type="submit">Add to Cart</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-books-message">
                <h3>No Books Available Yet</h3>
                <p>Our bookstore is getting ready! Please check back soon.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$footer = __DIR__ . '/includes/footer.php';
if (is_file($footer)) {
    include $footer;
}
?>

</body>
</html>
