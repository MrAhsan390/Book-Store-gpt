
<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

function respond($status, $message, $orderId = null) {
    $data = ['status' => $status, 'message' => $message];

    if ($orderId !== null) {
        $data['order_id'] = $orderId;
    }

    echo json_encode($data);
    exit;
}

if (!isset($_SESSION['userid'])) {
    http_response_code(401);
    respond('error', 'Please log in before placing an order.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    respond('error', 'Invalid request.');
}

require_once __DIR__ . '/../includes/attach.php';

$userid = (int) $_SESSION['userid'];
$fname = trim($_POST['fname'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');

if ($fname === '' || $email === '' || $phone === '' || $address === '') {
    respond('error', 'Please complete all checkout fields.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond('error', 'Please enter a valid email address.');
}

try {
    $conn->begin_transaction();

    $stmt = $conn->prepare(
        'SELECT c.bk_id, c.quantity, b.bk_price
         FROM cart c
         JOIN books b ON b.bk_id = c.bk_id
         WHERE c.user_id = ?
         FOR UPDATE'
    );
    $stmt->bind_param('i', $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    $total = 0;

    while ($item = $result->fetch_assoc()) {
        $item['bk_id'] = (int) $item['bk_id'];
        $item['quantity'] = (int) $item['quantity'];
        $item['bk_price'] = (float) $item['bk_price'];

        $items[] = $item;
        $total += $item['quantity'] * $item['bk_price'];
    }
    $stmt->close();

    if (!$items) {
        $conn->rollback();
        respond('error', 'Your cart is empty.');
    }

    $status = 'Pending';

    $stmt = $conn->prepare(
        'INSERT INTO orders
         (userid, fname, email, phone, address, total_amount, order_status)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->bind_param(
        'issssds',
        $userid,
        $fname,
        $email,
        $phone,
        $address,
        $total,
        $status
    );
    $stmt->execute();
    $orderId = (int) $conn->insert_id;
    $stmt->close();

    $stmt = $conn->prepare(
        'INSERT INTO order_items (order_id, bk_id, quantity, price)
         VALUES (?, ?, ?, ?)'
    );

    foreach ($items as $item) {
        $bkId = $item['bk_id'];
        $quantity = $item['quantity'];
        $price = $item['bk_price'];

        $stmt->bind_param(
            'iiid',
            $orderId,
            $bkId,
            $quantity,
            $price
        );
        $stmt->execute();
    }
    $stmt->close();

    $stmt = $conn->prepare('DELETE FROM cart WHERE user_id = ?');
    $stmt->bind_param('i', $userid);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

    respond('success', 'Order placed successfully!', $orderId);

} catch (Throwable $e) {
    try {
        $conn->rollback();
    } catch (Throwable $ignored) {
    }

    error_log('Order placement error: ' . $e->getMessage());
    http_response_code(500);
    respond('error', 'Order could not be placed. Please check the database configuration.');
}
