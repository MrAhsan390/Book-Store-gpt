cart/purchase.php – Checkout Page
<?php
session_start();
include("../includes/attach.php");
include("../includes/header.php");

if(!isset($_SESSION['userid'])){
    header("Location: ../login.php");
    exit;
}

$userid = $_SESSION['userid'];
$cart = $conn->query("SELECT c.cart_id as cart_id, b.* , c.quantity FROM cart c JOIN books b ON c.bk_id=b.bk_id WHERE c.user_id=$userid");

if($cart->num_rows == 0){
    echo "<script>alert('Your cart is empty!'); window.location='../books/booksShowUserMode.php';</script>";
    exit;
}

// Calculate total
$total = 0;
while($c = $cart->fetch_assoc()){
    $total += $c['bk_price'] * $c['quantity'];
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Checkout - Online Bookstore</title>
<style>
body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:0;}
.container { max-width:600px; margin:50px auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1);}
h2 { text-align:center; color:#2b6cb0; margin-bottom:20px; }
form { display:flex; flex-direction:column; }
label { margin:10px 0 5px; }
input, textarea { padding:10px; border-radius:5px; border:1px solid #ccc; }
button { padding:10px; margin-top:15px; border:none; border-radius:5px; background:#2b6cb0; color:white; cursor:pointer; }
button:hover { background:#1e4f8a; }
</style>
</head>
<body>

<div class="container">
<h2>Checkout</h2>
<p><strong>Total Amount:</strong> $<?php echo $total; ?></p>

<form id="checkoutForm">
<label for="fname">Full Name</label>
<input type="text" id="fname" name="fname" required>

<label for="email">Email</label>
<input type="email" id="email" name="email" required>

<label for="phone">Phone</label>
<input type="text" id="phone" name="phone" required>

<label for="address">Shipping Address</label>
<textarea id="address" name="address" rows="4" required></textarea>

<button type="submit">Place Order</button>
<div id="msg"></div>
</form>
</div>

<script>
// Handle checkout via AJAX
document.getElementById("checkoutForm").onsubmit = function(e){
    e.preventDefault();
    let formData = new FormData(this);
    fetch("orderHandler.php",{
        method:"POST",
        body: formData
    })
    .then(res=>res.json())
    .then(data=>{
        let msg = document.getElementById("msg");
        msg.innerText = data.message;
        msg.style.color = data.status=="success"?"green":"red";
        if(data.status=="success"){
            setTimeout(()=>{ window.location="orderConfirmation.php?id="+data.order_id; },1500);
        }
    });
};
</script>

</body>
</html>
