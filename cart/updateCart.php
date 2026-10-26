<?php
session_start();
require_once __DIR__ . "/../includes/attach.php";

header("Content-Type: application/json; charset=UTF-8");

function sendJson($status, $message) {
    echo json_encode([
        "status" => $status,
        "message" => $message
    ]);
    exit;
}

if (!isset($_SESSION['userid'])) {
    sendJson("error", "Please log in again.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson("error", "Invalid request.");
}

$userId = (int) $_SESSION['userid'];
$cartId = filter_var(
    $_POST['cart_id'] ?? '',
    FILTER_VALIDATE_INT
);

if (!$cartId || $cartId < 1) {
    sendJson("error", "Invalid cart item ID.");
}

// Remove item
if (isset($_POST['remove'])) {
    $stmt = $conn->prepare(
        "DELETE FROM cart WHERE cart_id = ? AND user_id = ?"
    );
    $stmt->bind_param("ii", $cartId, $userId);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $stmt->close();
        sendJson("success", "Book removed from cart.");
    }

    $stmt->close();
    sendJson("error", "The book could not be removed.");
}

// Update quantity
$quantity = filter_var(
    $_POST['quantity'] ?? '',
    FILTER_VALIDATE_INT
);

if ($quantity === false || $quantity === null || $quantity < 1) {
    sendJson("error", "Enter a valid quantity of at least 1.");
}

$stmt = $conn->prepare(
    "UPDATE cart
     SET quantity = ?
     WHERE cart_id = ? AND user_id = ?"
);
$stmt->bind_param("iii", $quantity, $cartId, $userId);

if (!$stmt->execute()) {
    $stmt->close();
    sendJson("error", "Database error while updating quantity.");
}

$stmt->close();

// Verify the saved quantity
$stmt = $conn->prepare(
    "SELECT quantity FROM cart
     WHERE cart_id = ? AND user_id = ?"
);
$stmt->bind_param("ii", $cartId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$savedItem = $result->fetch_assoc();
$stmt->close();

if (!$savedItem || (int)$savedItem['quantity'] !== $quantity) {
    sendJson("error", "The quantity was not saved. Please try again.");
}

sendJson("success", "Quantity updated to " . $quantity . ".");
?>