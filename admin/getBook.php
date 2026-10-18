admin/getBook.php – Fetch Single Book Data (AJAX)
<?php
session_start();
include("../includes/attach.php");

$response = [];

if(!isset($_SESSION['userid']) || $_SESSION['role']!=='admin'){
    echo json_encode($response);
    exit;
}

if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM books WHERE bk_id=$id");
    if($result->num_rows > 0){
        $response = $result->fetch_assoc();
    }
}

echo json_encode($response);

