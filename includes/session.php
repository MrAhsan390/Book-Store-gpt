<?php
// includes/session.php (v3 – final)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
