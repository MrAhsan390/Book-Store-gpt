
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/attach.php';

$baseUrl = '/project/BOOK-STORE-GPT';
$books = $conn->query('SELECT * FROM books ORDER BY bk_id DESC');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Book Catalog</title>

        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f4f4f4;
                margin: 0;
            }
            .container {
                max-width: 1200px;
                margin: 30px auto;
                padding: 20px;
            }
            h2 {
                text-align: center;
                color: #2b6cb0;
            }
            .search-bar {
                text-align: center;
                margin-bottom: 20px;
            }
            .search-bar input {
                max-width: 90%;
                width: 300px;
                padding: 10px;
                border-radius: 5px;
                border: 1px solid #ccc;
            }

            .search-bar button,
            .book-card button {
                padding: 9px 12px;
                border: 0;
                border-radius: 5px;
                color: white;
                background: #2b6cb0;
                cursor: pointer;
                margin: 5px 2px;
            }

            .books-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
                gap: 20px;
            }

            .book-card {
                background: white;
                border-radius: 10px;
                padding: 15px;
                text-align: center;
                box-shadow: 0 4px 10px rgba(0,0,0,.1);
            }

            .book-card img {
                max-width: 100%;
                height: 200px;
                object-fit: cover;
                border-radius: 5px;
            }

            .book-card h3 {
                color: #2b6cb0;
            }

            .book-card form {
                margin: 0;
            }

            .notice {
                text-align: center;
                color: #166534;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <?php include __DIR__ . '/../includes/header.php'; ?>
        <div class="container">
            <h2>Book Catalog</h2>

            <?php if (($_GET['cart'] ?? '') === 'added'): ?>
                <p class="notice">Book added to your cart successfully.</p>
            <?php endif; ?>

            <div class="search-bar">
                <input type="text" id="searchInput"
                    placeholder="Search books by title or author">
                <button type="button" id="searchBtn">Search</button>
                <button type="button" id="showAllBtn">Show All</button>
            </div>

            <div class="books-grid" id="booksGrid">
                <?php if ($books->num_rows > 0): ?>
                    <?php while ($book = $books->fetch_assoc()): ?>
                        <?php
                        $bookId = (int) $book['bk_id'];
                        $imageName = basename($book['bk_image'] ?? '');
                        $imagePath = __DIR__ . '/../assets/uploads/books/' . $imageName;

                        $imageUrl = $baseUrl . '/assets/uploads/books/' .
                                    rawurlencode($imageName);

                        if ($imageName === '' || !is_file($imagePath)) {
                            $imageUrl = $baseUrl . '/assets/images/no-image.png';
                        }

                        $returnTo = $baseUrl . '/books/booksShowUserMode.php?cart=added';
                        ?>

                        <div class="book-card"
                            data-title="<?= htmlspecialchars($book['bk_title']) ?>"
                            data-author="<?= htmlspecialchars($book['bk_author']) ?>">

                            <img src="<?= htmlspecialchars($imageUrl) ?>"
                                alt="<?= htmlspecialchars($book['bk_title']) ?>">

                            <h3><?= htmlspecialchars($book['bk_title']) ?></h3>
                            <p>Author: <?= htmlspecialchars($book['bk_author']) ?></p>
                            <p>Edition: <?= htmlspecialchars($book['bk_eddition']) ?></p>
                            <p>Price: $<?= number_format((float)$book['bk_price'], 2) ?></p>

                            <a href="<?= $baseUrl ?>/books/bookDetails.php?id=<?= $bookId ?>">
                                <button type="button" class="detailsBtn">View Details</button>
                            </a>

                            <form method="post"
                                action="<?= $baseUrl ?>/cart/addToCart.php">
                                <input type="hidden" name="bk_id"
                                    value="<?= $bookId ?>">
                                <input type="hidden" name="return_to"
                                    value="<?= htmlspecialchars($returnTo) ?>">
                                <button type="submit" class="cartBtn">Add to Cart</button>
                            </form>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No books available.</p>
                <?php endif; ?>
            </div>
        </div>

        <script>
        function filterBooks() {
            const query = document.getElementById('searchInput')
                .value.trim().toLowerCase();

            document.querySelectorAll('.book-card').forEach(card => {
                const title = (card.dataset.title || '').toLowerCase();
                const author = (card.dataset.author || '').toLowerCase();

                card.style.display =
                    title.includes(query) || author.includes(query) ? '' : 'none';
            });
        }

        document.getElementById('searchBtn').addEventListener('click', filterBooks);

        document.getElementById('searchInput').addEventListener('keydown', event => {
            if (event.key === 'Enter') {
                event.preventDefault();
                filterBooks();
            }
        });

        document.getElementById('showAllBtn').addEventListener('click', () => {
            document.getElementById('searchInput').value = '';
            filterBooks();
        });
        </script>

        <?php
        $footer = __DIR__ . '/../includes/footer.php';
        if (is_file($footer)) {
            include $footer;
        }
        ?>
    </body>
</html>
