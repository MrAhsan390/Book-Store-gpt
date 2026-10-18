1️⃣ admin/orders.php – Admin Orders Dashboard
<?php
session_start();
include("../includes/attach.php");
include("../includes/header.php");

// Only admin check (assume is_admin in session)
if(!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1){
    header("Location: ../login.php");
    exit;
}

$orders = $conn->query("SELECT * FROM orders ORDER BY order_id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Orders - Online Bookstore</title>
<style>
body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:0;}
.container { max-width:1000px; margin:30px auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1);}
h2 { text-align:center; color:#2b6cb0; margin-bottom:20px; }
table { width:100%; border-collapse: collapse; }
th, td { border:1px solid #ccc; padding:10px; text-align:center; }
th { background:#2b6cb0; color:white; }
button { padding:5px 10px; border:none; border-radius:5px; cursor:pointer; background:#2b6cb0; color:white; }
button:hover { background:#1e4f8a; }
select { padding:5px; border-radius:5px; }
</style>
</head>
<body>

<div class="container">
<h2>All Orders</h2>
<table>
<tr>
<th>Order ID</th>
<th>User</th>
<th>Total</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php
while($o = $orders->fetch_assoc()){
    echo "<tr>";
    echo "<td>".$o['order_id']."</td>";
    echo "<td>".$o['fname']."</td>";
    echo "<td>$".$o['total']."</td>";
    echo "<td>".$o['status']."</td>";
    echo "<td>
            <button onclick=\"window.location='orderDetails.php?id=".$o['order_id']."'\">View</button>
            <select onchange=\"updateStatus(".$o['order_id'].", this.value)\">
                <option value='Pending' ".($o['status']=='Pending'?'selected':'').">Pending</option>
                <option value='Shipped' ".($o['status']=='Shipped'?'selected':'').">Shipped</option>
                <option value='Delivered' ".($o['status']=='Delivered'?'selected':'').">Delivered</option>
            </select>
          </td>";
    echo "</tr>";
}
?>
</table>
</div>

<script>
function updateStatus(order_id, status){
    fetch("updateOrder.php",{
        method:"POST",
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:"order_id="+order_id+"&status="+status
    })
    .then(res=>res.json())
    .then(data=>{
        alert(data.message);
        if(data.status=="success"){ location.reload(); }
    });
}
</script>

</body>
</html>
