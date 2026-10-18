2️⃣ admin/orderDetails.php – Detailed Order View
<?php
session_start();
include("../includes/attach.php");
include("../includes/header.php");

if(!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1){
    header("Location: ../login.php");
    exit;
}

if(!isset($_GET['id'])){ echo "Invalid order ID"; exit; }

$order_id = intval($_GET['id']);
$order_res = $conn->query("SELECT * FROM orders WHERE order_id=$order_id");
$order = $order_res->fetch_assoc();

$items_res = $conn->query("SELECT oi.*, b.bk_title FROM order_items oi JOIN books b ON oi.bk_id=b.bk_id WHERE oi.order_id=$order_id");
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Order Details - Admin</title>
        <style>

        body { 
            font-family: Arial, sans-serif; 
            background:#f4f4f4; 
            margin:0; 
            padding:0; 
        }

        .container { 
            max-width:800px; 
            margin:50px auto; 
            background:#fff; 
            padding:20px; 
            border-radius:10px; 
            box-shadow:0 4px 10px rgba(0,0,0,0.1);
        }

        h2 { 
        text-align:center; 
        color:#2b6cb0; 
        margin-bottom:20px; 
        }

        table { 
            width:100%; 
            border-collapse: collapse; 
            margin-top:20px;
        }


        th, td { 
            border:1px solid #ccc; 
            padding:10px; 
            text-align:center; 
        }

        th {
        background:#2b6cb0; 
        color:white; 
        }

        </style>
    </head>
    <body>

        <div class="container">
            <h2>Order #<?php echo $order['order_id']; ?> Details</h2>

                <p><strong>User:</strong> <?php echo $order['fname']; ?></p>
                <p><strong>Email:</strong> <?php echo $order['email']; ?></p>
                <p><strong>Phone:</strong> <?php echo $order['phone']; ?></p>
                <p><strong>Address:</strong> <?php echo $order['address']; ?></p>
                <p><strong>Status:</strong> <?php echo $order['status']; ?></p>
                <p><strong>Total:</strong> $<?php echo $order['total']; ?></p>

                <h3>Items</h3>        
            <table>
                <tr><th>Book</th><th>Quantity</th><th>Price</th><th>Subtotal</th></tr>
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

        </div>

    </body>
</html>
