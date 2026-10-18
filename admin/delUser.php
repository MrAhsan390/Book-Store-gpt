admin/delUser.php – Delete User (AJAX)
<?php
session_start();
include("../includes/attach.php");

$response = ['status'=>'error','message'=>'Invalid request'];

// Admin check
if(!isset($_SESSION['userid']) || $_SESSION['role']!=='admin'){
    echo json_encode($response);
    exit;
}

if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    if($conn->query("DELETE FROM users WHERE userid=$id")){
        $response['status'] = 'success';
        $response['message'] = "User deleted successfully!";
    } else {
        $response['message'] = "Database error: ".$conn->error;
    }
}

echo json_encode($response);

