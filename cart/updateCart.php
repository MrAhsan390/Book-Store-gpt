<?php
session_start();
require_once __DIR__ . "/../includes/attach.php";

header("Content-Type: application/json; charset=UTF-8");

function respond($status, $message) {
    echo json_encode([
        "status" => $status,
        "message" => $message
    ]);
    exit;
}

if (!isset($_SESSION['userid'])) {
    respond("error", "Please log in to manage your cart.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond("error", "Invalid request method.");
}

$user_id = (int) $_SESSION['userid'];
$cart_id = filter_input(INPUT_POST, 'cart_id', FILTER_VALIDATE_INT);

if (!$cart_id || $cart_id < 1) {
    respond("error", "Invalid cart item.");
}

// Confirm this cart item belongs to the logged-in user.
$stmt = $conn->prepare(
    "SELECT cart_id FROM cart WHERE cart_id = ? AND user_id = ?"
);
$stmt->bind_param("ii", $cart_id, $user_id);
$stmt->execute();
$exists = $stmt->get_result()->num_rows > 0;
$stmt->close();

if (!$exists) {
    respond("error", "Cart item not found.");
}

if (isset($_POST['remove'])) {
    $stmt = $conn->prepare(
        "DELETE FROM cart WHERE cart_id = ? AND user_id = ?"
    );
    $stmt->bind_param("ii", $cart_id, $user_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $stmt->close();
        respond("success", "Book removed from your cart.");
    }

    $stmt->close();
    respond("error", "Could not remove the book.");
}

$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

if (!$quantity || $quantity < 1) {
    respond("error", "Quantity must be at least 1.");
}

$stmt = $conn->prepare(
    "UPDATE cart
     SET quantity = ?
     WHERE cart_id = ? AND user_id = ?"
);
$stmt->bind_param("iii", $quantity, $cart_id, $user_id);

if ($stmt->execute()) {
    $stmt->close();
    respond("success", "Cart quantity updated successfully.");
}

$stmt->close();
respond("error", "Could not update the cart.");
?>