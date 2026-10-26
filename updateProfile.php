<?php
session_start();
require_once __DIR__ . "/includes/attach.php";

header("Content-Type: application/json; charset=UTF-8");

function sendResponse($status, $message) {
    echo json_encode([
        "status" => $status,
        "message" => $message
    ]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    sendResponse("error", "Invalid request method.");
}

if (!isset($_SESSION["userid"])) {
    sendResponse("error", "Please log in again.");
}

$user_id = (int) $_SESSION["userid"];
$username = trim($_POST["username"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($username === "" || $email === "") {
    sendResponse("error", "Username and email are required.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendResponse("error", "Please enter a valid email address.");
}

// Check whether another user already uses this email.
$stmt = $conn->prepare(
    "SELECT userid FROM users WHERE email = ? AND userid != ? LIMIT 1"
);
$stmt->bind_param("si", $email, $user_id);
$stmt->execute();
$emailResult = $stmt->get_result();
$emailExists = $emailResult->num_rows > 0;
$stmt->close();

if ($emailExists) {
    sendResponse("error", "This email address is already in use.");
}

if ($password !== "") {
    if (strlen($password) < 8) {
        sendResponse("error", "Your new password must be at least 8 characters.");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare(
        "UPDATE users
         SET username = ?, email = ?, password = ?
         WHERE userid = ?"
    );
    $stmt->bind_param(
        "sssi",
        $username,
        $email,
        $hashedPassword,
        $user_id
    );
} else {
    $stmt = $conn->prepare(
        "UPDATE users
         SET username = ?, email = ?
         WHERE userid = ?"
    );
    $stmt->bind_param("ssi", $username, $email, $user_id);
}

if ($stmt->execute()) {
    $_SESSION["username"] = $username;
    $stmt->close();
    sendResponse("success", "Profile updated successfully!");
}

$stmt->close();
sendResponse("error", "Unable to update your profile. Please try again.");
?>
