
<?php
// Local development database setup.
// Remove this file or disable it before deployment.

if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit('This setup page is available only on localhost.');
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'book-store-gpt';

$message = '';
$error = '';

try {
    // Connect to MySQL without selecting a database.
    $conn = new mysqli($host, $username, $password);
    $conn->set_charset('utf8mb4');

    // Create the database if it does not exist.
    $conn->query(
        "CREATE DATABASE IF NOT EXISTS `book-store-gpt`
         CHARACTER SET utf8mb4
         COLLATE utf8mb4_unicode_ci"
    );

    $conn->select_db($database);

    // Books table.
    $conn->query("
        CREATE TABLE IF NOT EXISTS books (
            bk_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            bk_title VARCHAR(255) NOT NULL,
            bk_author VARCHAR(150) NOT NULL,
            bk_eddition VARCHAR(100) DEFAULT NULL,
            bk_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            bk_description TEXT DEFAULT NULL,
            bk_image VARCHAR(255) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB
    ");

    // Registered users table.
    $conn->query("
        CREATE TABLE IF NOT EXISTS users (
            userid INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) NOT NULL,
            email VARCHAR(190) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB
    ");

    // Shopping cart table.
    $conn->query("
        CREATE TABLE IF NOT EXISTS cart (
            cart_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            bk_id INT UNSIGNED NOT NULL,
            quantity INT UNSIGNED NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_book (user_id, bk_id),
            CONSTRAINT fk_cart_user
                FOREIGN KEY (user_id)
                REFERENCES users(userid)
                ON DELETE CASCADE,
            CONSTRAINT fk_cart_book
                FOREIGN KEY (bk_id)
                REFERENCES books(bk_id)
                ON DELETE CASCADE
        ) ENGINE=InnoDB
    ");

    $message = 'Database and initial tables created successfully.';

} catch (Throwable $e) {
    error_log($e->getMessage());
    $error = 'Setup failed. Check your PHP error log for details.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Store - Database Setup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 40px 16px;
        }
        .container {
            max-width: 650px;
            margin: auto;
            background: white;
            padding: 28px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,.08);
        }
        h1 { color: #2b6cb0; }
        .success { color: #176b38; }
        .error { color: #b42318; }
        a { color: #2b6cb0; }
    </style>
</head>
<body>
    <main class="container">
        <h1>Book Store Database Setup</h1>

        <?php if ($message !== ''): ?>
            <p class="success">
                <?= htmlspecialchars($message) ?>
            </p>
            <p>Database: <strong>book-store-gpt</strong></p>
            <p>Tables created or already present: books, users, cart.</p>
            <p>
                <a href="index.php">Open the bookstore homepage</a>
            </p>
        <?php elseif ($error !== ''): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
            <p>Check that MySQL is running and the credentials are correct.</p>
        <?php endif; ?>
    </main>
</body>
</html>
