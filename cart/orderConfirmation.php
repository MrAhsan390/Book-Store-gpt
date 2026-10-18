cart/orderConfirmation.php – Order Confirmation
<?php
session_start();
include("../includes/attach.php");
include("../includes/header.php");

if(!isset($_GET['id'])){
    echo "Invalid order ID.";
    exit;
}

$order_id = intval($_GET['id']);

// Get order details
$order_res = $conn->query("SELECT * FROM orders WHERE order_id=$order_id");
if($order_res->num_rows == 0){
    echo "Order not found.";
    exit;
}
$order = $order_res->fetch_assoc();

// Get order items
$items_res = $conn->query("SELECT oi.*, b.bk_title FROM order_items oi JOIN books b ON oi.bk_id=b.bk_id WHERE oi.order_id=$order_id");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Order Confirmation - Online Bookstore</title>
<style>
body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:0;}
.container { max-width:800px; margin:50px auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1);}
h2 { text-align:center; color:#2b6cb0; margin-bottom:20px; }
table { width:100%; border-collapse: collapse; margin-top:20px; }
th, td { border:1px solid #ccc; padding:10px; text-align:center; }
th { background:#2b6cb0; color:white; }
p { margin:5px 0; }
</style>
</head>
<body>

<div class="container">
<h2>Order Confirmation</h2>

<p><strong>Order ID:</strong> <?php echo $order['order_id']; ?></p>
<p><strong>Name:</strong> <?php echo $order['fname']; ?></p>
<p><strong>Email:</strong> <?php echo $order['email']; ?></p>
<p><strong>Phone:</strong> <?php echo $order['phone']; ?></p>
<p><strong>Address:</strong> <?php echo $order['address']; ?></p>
<p><strong>Status:</strong> <?php echo $order['status']; ?></p>
<p><strong>Total Amount:</strong> $<?php echo $order['total']; ?></p>

<h3>Order Items</h3>
<table>
<tr>
<th>Book</th>
<th>Quantity</th>
<th>Price</th>
<th>Subtotal</th>
</tr>
<?php
while($item = $items_res->fetch_assoc()){
    $subtotal = $item['quantity'] * $item['price'];
    echo "<tr>";
    echo "<td>".$item['bk_title']."</td>";
    echo "<td>".$item['quantity']."</td>";
    echo "<td>$".$item['price']."</td>";
    echo "<td>$".$subtotal."</td>";
    echo "</tr>";
}
?>
</table>

<p style="text-align:center; margin-top:20px;">Thank you for shopping with us!</p>
</div>

</body>
</html>

