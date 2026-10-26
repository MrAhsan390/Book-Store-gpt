
<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function respond(string $status, string $message, int $httpCode = 200): void
{
    http_response_code($httpCode);

    echo json_encode([
        'status' => $status,
        'message' => $message
    ]);

    exit;
}

try {
    require __DIR__ . '/includes/attach.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        respond('error', 'Invalid request method.', 405);
    }

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($username === '' || $email === '' ||
        $password === '' || $confirmPassword === '') {
        respond('error', 'Please fill in all fields.', 400);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        respond('error', 'Please enter a valid email address.', 400);
    }

    if ($password !== $confirmPassword) {
        respond('error', 'Passwords do not match.', 400);
    }

    if (strlen($password) < 8) {
        respond('error', 'Your password must be at least 8 characters long.', 400);
    }

    $stmt = $conn->prepare(
        'SELECT userid FROM users WHERE email = ?'
    );
    $stmt->bind_param('s', $email);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        $stmt->close();
        respond('error', 'This email is already registered.', 409);
    }

    $stmt->close();

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare(
        'INSERT INTO users (username, email, password)
         VALUES (?, ?, ?)'
    );

    $stmt->bind_param('sss', $username, $email, $passwordHash);
    $stmt->execute();
    $stmt->close();

    respond(
        'success',
        'Registration successful! Redirecting to login...'
    );

} catch (Throwable $e) {
    error_log('Registration error: ' . $e->getMessage());

    respond(
        'error',
        'Registration failed due to a server error. Please check the PHP error log.',
        500
    );
}
