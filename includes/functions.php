<?php
// includes/functions.php (v3 – final)

// Secure input
function cleanInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Check login
function checkLogin() {
    if (!isset($_SESSION['username'])) {
        header("Location: /bookstore/login.php");
        exit();
    }
}

// Flash message system
function setFlash($msg, $type = "success") {
    $_SESSION['flash'] = ["msg" => $msg, "type" => $type];
}

function showFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        echo "<div style='padding:10px; margin:10px 0; background:" 
            . ($flash['type'] == "success" ? "#d4edda; color:#155724;" : "#f8d7da; color:#721c24;") 
            . "'>{$flash['msg']}</div>";
        unset($_SESSION['flash']);
    }
}
?>
