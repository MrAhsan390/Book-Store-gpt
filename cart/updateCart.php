cart/updateCart.php – Update Quantity / Remove Item
<?php
session_start();
include("../includes/attach.php");

$response = ['status'=>'error','message'=>'Invalid request'];

if(!isset($_SESSION['userid'])){
    echo json_encode($response);
    exit;
}

$userid = $_SESSION['userid'];

if(isset($_POST['cart_id'])){
    $cart_id = intval($_POST['cart_id']);

    // Remove item
    if(isset($_POST['remove'])){
        if($conn->query("DELETE FROM cart WHERE id=$cart_id AND user_id=$userid")){
            $response = ['status'=>'success','message'=>'Item removed from cart.'];
        } else {
            $response['message'] = $conn->error;
        }
    }
    // Update quantity
    elseif(isset($_POST['quantity'])){
        $qty = intval($_POST['quantity']);
        if($qty < 1) $qty = 1;
        if($conn->query("UPDATE cart SET quantity=$qty WHERE id=$cart_id AND user_id=$userid")){
            $response = ['status'=>'success','message'=>'Quantity updated.'];
        } else {
            $response['message'] = $conn->error;
        }
    }
}

echo json_encode($response);

