
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/attach.php';

$baseUrl = '/project/BOOK-STORE-GPT';
$bookId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$book = null;

if ($bookId && $bookId > 0) {
    $stmt = $conn->prepare('SELECT * FROM books WHERE bk_id = ?');
    $stmt->bind_param('i', $bookId);
    $stmt->execute();
    $book = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $book ? htmlspecialchars($book['bk_title']) . ' - ' : '' ?>
        Book Details
    </title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
        }

        .container {
            max-width: 850px;
            margin: 40px auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,.1);
            overflow: auto;
        }

        .book-img {
            width: 230px;
            max-width: 100%;
            max-height: 320px;
            object-fit: contain;
            float: left;
            margin: 0 25px 15px 0;
        }

        h2 {
            color: #2b6cb0;
        }

        button {
            padding: 10px 15px;
            border: 0;
            border-radius: 5px;
            cursor: pointer;
            background: #2b6cb0;
            color: white;
            margin: 5px 5px 5px 0;
        }

        .notice {
            color: #166534;
            font-weight: bold;
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="container">
    <?php if (!$book): ?>
        <h2>Book not found</h2>
        <p>The book ID is invalid, or this book no longer exists.</p>
        <a href="<?= $baseUrl ?>/index.php">Return to homepage</a>
    <?php else: ?>
        <?php
        $imageName = basename($book['bk_image'] ?? '');
        $imagePath = __DIR__ . '/../assets/uploads/books/' . $imageName;

        $imageUrl = $baseUrl . '/assets/uploads/books/' .
                    rawurlencode($imageName);

        if ($imageName === '' || !is_file($imagePath)) {
            $imageUrl = $baseUrl . '/assets/images/no-image.png';
        }

        $returnTo = $baseUrl . '/books/bookDetails.php?id=' .
                    (int)$book['bk_id'] . '&cart=added';
        ?>

        <h2><?= htmlspecialchars($book['bk_title']) ?></h2>

        <?php if (($_GET['cart'] ?? '') === 'added'): ?>
            <p class="notice">Book added to your cart successfully.</p>
        <?php endif; ?>

        <img class="book-img"
             src="<?= htmlspecialchars($imageUrl) ?>"
             alt="<?= htmlspecialchars($book['bk_title']) ?>">
        <br>
        <p><strong>Author:</strong>
            <?= htmlspecialchars($book['bk_author']) ?></p>

        <p><strong>Edition:</strong>
            <?= htmlspecialchars($book['bk_eddition']) ?></p>

        <p><strong>Price:</strong>
            $<?= number_format((float)$book['bk_price'], 2) ?></p>

        <p><strong>Description:</strong>
            <?= htmlspecialchars(
                !empty($book['bk_description'])
                    ? $book['bk_description']
                    : 'No description available.'
            ) ?></p>

        <form method="post" action="<?= $baseUrl ?>/cart/addToCart.php">
            <input type="hidden" name="bk_id"
                   value="<?= (int)$book['bk_id'] ?>">
            <input type="hidden" name="return_to"
                   value="<?= htmlspecialchars($returnTo) ?>">
            <button type="submit">Add to Cart</button>
        </form>

        <p><a href="<?= $baseUrl ?>/books/booksShowUserMode.php">
            Back to Book Catalog
        </a></p>
    <?php endif; ?>
</div>

</body>
</html>
