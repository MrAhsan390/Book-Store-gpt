
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$baseUrl = '/project/BOOK-STORE-GPT';

// Visitors must log in before adding books to their cart.
if (!isset($_SESSION['userid'])) {
    header('Location: ' . $baseUrl . '/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $baseUrl . '/books/booksShowUserMode.php');
    exit;
}

require_once __DIR__ . '/../includes/attach.php';

$userId = (int) $_SESSION['userid'];
$bookId = filter_input(INPUT_POST, 'bk_id', FILTER_VALIDATE_INT);

// Only allow return URLs within this bookstore.
$returnTo = $_POST['return_to'] ?? ($baseUrl . '/index.php');
$returnPath = parse_url($returnTo, PHP_URL_PATH);

if (
    !is_string($returnTo) ||
    strpos($returnTo, '://') !== false ||
    !is_string($returnPath) ||
    strpos($returnPath, $baseUrl . '/') !== 0 ||
    strpos($returnPath, '..') !== false
) {
    $returnTo = $baseUrl . '/index.php';
}

try {
    if (!$bookId || $bookId < 1) {
        throw new RuntimeException('Invalid book ID.');
    }

    // Verify that the requested book exists.
    $stmt = $conn->prepare(
        'SELECT bk_id FROM books WHERE bk_id = ?'
    );
    $stmt->bind_param('i', $bookId);
    $stmt->execute();

    $bookExists = $stmt->get_result()->num_rows > 0;
    $stmt->close();

    if (!$bookExists) {
        throw new RuntimeException('This book does not exist.');
    }

    // Check whether this book is already in this user's cart.
    $stmt = $conn->prepare(
        'SELECT cart_id
         FROM cart
         WHERE user_id = ? AND bk_id = ?
         LIMIT 1'
    );
    $stmt->bind_param('ii', $userId, $bookId);
    $stmt->execute();

    $existingCartItem = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($existingCartItem) {
        // Increase the existing quantity by one.
        $cartId = (int) $existingCartItem['cart_id'];

        $stmt = $conn->prepare(
            'UPDATE cart
             SET quantity = quantity + 1
             WHERE cart_id = ? AND user_id = ?'
        );
        $stmt->bind_param('ii', $cartId, $userId);
        $stmt->execute();
        $stmt->close();
    } else {
        // Add this book to the user's cart.
        $quantity = 1;

        $stmt = $conn->prepare(
            'INSERT INTO cart (user_id, bk_id, quantity)
             VALUES (?, ?, ?)'
        );
        $stmt->bind_param('iii', $userId, $bookId, $quantity);
        $stmt->execute();
        $stmt->close();
    }

    // The header will calculate the updated count from MySQL.
    header('Location: ' . $returnTo);
    exit;

} catch (Throwable $e) {
    error_log('Add to cart error: ' . $e->getMessage());

    http_response_code(500);
    echo 'Unable to add this book to your cart. Please check the PHP error log.';
    exit;
}
?>
