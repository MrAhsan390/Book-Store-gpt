
<?php
session_start();

header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function respond(string $status, string $message, int $code = 200): void {
    http_response_code($code);
    echo json_encode([
        'status' => $status,
        'message' => $message
    ]);
    exit;
}

try {
    require_once __DIR__ . '/../includes/attach.php';

    // Admin check
    if (
        !isset($_SESSION['userid']) ||
        ($_SESSION['role'] ?? '') !== 'admin'
    ) {
        respond('error', 'Admin access required. Please log in as an admin.', 403);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        respond('error', 'Invalid request method.', 405);
    }

    $title = trim($_POST['bk_title'] ?? '');
    $author = trim($_POST['bk_author'] ?? '');
    $eddition = trim($_POST['bk_eddition'] ?? '');
    $price = filter_var($_POST['bk_price'] ?? null, FILTER_VALIDATE_FLOAT);

    if ($title === '' || $author === '' || $eddition === '' ||
        $price === false || $price < 0) {
        respond('error', 'Please provide valid book details.', 400);
    }

    if (!isset($_FILES['bk_image']) ||
        $_FILES['bk_image']['error'] !== UPLOAD_ERR_OK) {
        respond('error', 'Please select an image to upload.', 400);
    }

    $image = $_FILES['bk_image'];

    if ($image['size'] > 5 * 1024 * 1024) {
        respond('error', 'Image must be 5 MB or smaller.', 400);
    }

    $imageInfo = getimagesize($image['tmp_name']);
    if ($imageInfo === false) {
        respond('error', 'The uploaded file must be a valid image.', 400);
    }

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    $mime = $imageInfo['mime'];
    if (!isset($allowedTypes[$mime])) {
        respond('error', 'Use a JPG, PNG, or WEBP image.', 400);
    }

    $uploadDir = __DIR__ . '/../assets/uploads/books/';

    if (!is_dir($uploadDir) &&
        !mkdir($uploadDir, 0755, true) &&
        !is_dir($uploadDir)) {
        respond('error', 'Could not create the image upload directory.', 500);
    }

    $newName = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$mime];
    $target = $uploadDir . $newName;

    if (!move_uploaded_file($image['tmp_name'], $target)) {
        respond('error', 'Image upload failed.', 500);
    }

    $stmt = $conn->prepare(
        'INSERT INTO books
        (bk_title, bk_author, bk_eddition, bk_price, bk_image)
        VALUES (?, ?, ?, ?, ?)'
    );

    $stmt->bind_param('sssds', $title, $author, $eddition, $price, $newName);
    $stmt->execute();
    $stmt->close();

    respond('success', 'Book added successfully!');
} catch (Throwable $e) {
    error_log('Add book error: ' . $e->getMessage());
    respond('error', 'A server error occurred while adding the book.', 500);
}
?>
