3️⃣ admin/updateOrder.php – Update Order Status (AJAX)
<?php
session_start();
include("../includes/attach.php");

$response = ['status'=>'error','message'=>'Invalid request'];

if(!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1){
    echo json_encode($response);
    exit;
}

if(isset($_POST['order_id'], $_POST['status'])){
    $order_id = intval($_POST['order_id']);
    $status = $conn->real_escape_string($_POST['status']);

    if($conn->query("UPDATE orders SET status='$status' WHERE order_id=$order_id")){
        $response = ['status'=>'success','message'=>'Order status updated.'];
    } else {
        $response['message'] = $conn->error;
    }
}

echo json_encode($response);
