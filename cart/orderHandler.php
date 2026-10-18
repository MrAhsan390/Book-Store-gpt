cart/orderHandler.php – Process Checkout & Save Order
<?php
session_start();
include("../includes/attach.php");

$response = ['status'=>'error','message'=>'Invalid request'];

if(!isset($_SESSION['userid'])){
    echo json_encode($response);
    exit;
}

$userid = $_SESSION['userid'];

if(isset($_POST['fname'], $_POST['email'], $_POST['phone'], $_POST['address'])){
    $fname = $conn->real_escape_string($_POST['fname']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);

    // Get cart items
    $cart = $conn->query("SELECT c.bk_id, c.quantity, b.bk_price FROM cart c JOIN books b ON c.bk_id=b.bk_id WHERE c.user_id=$userid");
    if($cart->num_rows == 0){
        $response['message'] = "Cart is empty.";
        echo json_encode($response);
        exit;
    }

    // Calculate total
    $total = 0;
    while($c = $cart->fetch_assoc()){
        $total += $c['quantity'] * $c['bk_price'];
    }

    // Insert into orders table
    $stmt = $conn->prepare("INSERT INTO orders(user_id,fname,email,phone,address,total,status) VALUES(?,?,?,?,?,?,?)");
    $status = "Pending";
    $stmt->bind_param("issssds",$userid,$fname,$email,$phone,$address,$total,$status);
    if($stmt->execute()){
        $order_id = $stmt->insert_id;

        // Insert order items
        $cart = $conn->query("SELECT c.bk_id, c.quantity, b.bk_price FROM cart c JOIN books b ON c.bk_id=b.bk_id WHERE c.user_id=$userid");
        while($c = $cart->fetch_assoc()){
            $stmt2 = $conn->prepare("INSERT INTO order_items(order_id,bk_id,quantity,price) VALUES(?,?,?,?)");
            $stmt2->bind_param("iiid",$order_id,$c['bk_id'],$c['quantity'],$c['bk_price']);
            $stmt2->execute();
        }

        // Clear cart
        $conn->query("DELETE FROM cart WHERE user_id=$userid");

        $response = ['status'=>'success','message'=>'Order placed successfully!','order_id'=>$order_id];
    } else {
        $response['message'] = "Database error: ".$conn->error;
    }
}

echo json_encode($response);
