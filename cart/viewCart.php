cart/viewCart.php – View & Manage Cart
<?php
session_start();
include("../includes/attach.php");
include("../includes/header.php");

if(!isset($_SESSION['userid'])){
    header("Location: ../login.php");
    exit;
}

$userid = $_SESSION['userid'];
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Your Cart - Online Bookstore</title>
<style>
body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:0;}
.container { max-width:900px; margin:30px auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1);}
h2 { text-align:center; color:#2b6cb0; margin-bottom:20px; }
table { width:100%; border-collapse: collapse; }
th, td { border:1px solid #ccc; padding:10px; text-align:center; }
th { background:#2b6cb0; color:white; }
button { padding:6px 12px; border:none; border-radius:5px; cursor:pointer; }
.updateBtn { background:#38a169; color:white; }
.updateBtn:hover { background:#2f855a; }
.delBtn { background:#e53e3e; color:white; }
.delBtn:hover { background:#c53030; }
#total { text-align:right; font-weight:bold; margin-top:10px; }
.checkoutBtn { background:#2b6cb0; color:white; padding:10px 15px; margin-top:10px; float:right; }
.checkoutBtn:hover { background:#1e4f8a; }
</style>
</head>
<body>

<div class="container">
<h2>Your Cart</h2>
<table id="cartTable">
<tr>
<th>Book</th>
<th>Price</th>
<th>Quantity</th>
<th>Subtotal</th>
<th>Actions</th>
</tr>

<?php
$cart = $conn->query("SELECT c.id as cart_id, b.* , c.quantity FROM cart c JOIN books b ON c.bk_id=b.bk_id WHERE c.user_id=$userid");
$total = 0;
if($cart->num_rows > 0){
    while($c = $cart->fetch_assoc()){
        $subtotal = $c['bk_price'] * $c['quantity'];
        $total += $subtotal;
        echo "<tr data-id='".$c['cart_id']."'>";
        echo "<td>".$c['bk_title']."</td>";
        echo "<td>$".$c['bk_price']."</td>";
        echo "<td><input type='number' min='1' value='".$c['quantity']."' class='qtyInput' style='width:50px;'></td>";
        echo "<td class='subtotal'>$".$subtotal."</td>";
        echo "<td>
                <button class='updateBtn'>Update</button>
                <button class='delBtn'>Remove</button>
              </td>";
        echo "</tr>";
    }
}else{
    echo "<tr><td colspan='5'>Your cart is empty.</td></tr>";
}
?>
</table>
<div id="total">Total: $<?php echo $total; ?></div>
<button class="checkoutBtn" onclick="window.location='purchase.php'">Proceed to Checkout</button>
</div>

<script>
// Update quantity
document.querySelectorAll(".updateBtn").forEach(btn=>{
    btn.onclick = ()=>{
        let tr = btn.closest("tr");
        let cart_id = tr.dataset.id;
        let qty = tr.querySelector(".qtyInput").value;
        fetch("updateCart.php",{
            method:"POST",
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: "cart_id="+cart_id+"&quantity="+qty
        })
        .then(res=>res.json())
        .then(data=>{
            alert(data.message);
            if(data.status=="success"){ location.reload(); }
        });
    }
});

// Remove item
document.querySelectorAll(".delBtn").forEach(btn=>{
    btn.onclick = ()=>{
        if(confirm("Are you sure to remove this item?")){
            let tr = btn.closest("tr");
            let cart_id = tr.dataset.id;
            fetch("updateCart.php",{
                method:"POST",
                headers: {'Content-Type':'application/x-www-form-urlencoded'},
                body: "cart_id="+cart_id+"&remove=1"
            })
            .then(res=>res.json())
            .then(data=>{
                alert(data.message);
                if(data.status=="success"){ location.reload(); }
            });
        }
    }
});
</script>

</body>
</html>
