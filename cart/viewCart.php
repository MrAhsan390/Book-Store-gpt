<?php
// <!-- cart/viewCart.php – View & Manage Cart -->
session_start();

if(!isset($_SESSION['userid'])){
    header("Location: ../login.php");
    exit;
}

include("../includes/attach.php");
include("../includes/header.php");

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
                    $cart = $conn->query("SELECT c.cart_id as cart_id, b.* , c.quantity FROM cart c JOIN books b ON c.bk_id=b.bk_id WHERE c.user_id=$userid");
                    $total = 0;
                    if($cart->num_rows > 0){
                        while ($c = $cart->fetch_assoc()) {
                            $subtotal = (float)$c['bk_price'] * (int)$c['quantity'];
                            $total += $subtotal;
                            $cartId = (int)$c['cart_id'];
                            $title = htmlspecialchars($c['bk_title'], ENT_QUOTES, 'UTF-8');
                            $price = number_format((float)$c['bk_price'], 2);
                            $quantity = (int)$c['quantity'];
                            echo "<tr data-id='{$cartId}'>";
                            echo "<td>{$title}</td>";
                            echo "<td>$" . $price . "</td>";
                            echo "<td>
                                <input
                                    type='number'
                                    min='1'
                                    value='{$quantity}'
                                    class='qtyInput'
                                    style='width:60px;'
                                >
                            </td>";
                            echo "<td class='subtotal'>$" .
                                number_format($subtotal, 2) . "</td>";
                            echo "<td>
                                    <button
                                        type='button'
                                        class='updateBtn'
                                        data-cart-id='{$cartId}'
                                    >Update</button>
                                    <button
                                        type='button'
                                        class='delBtn'
                                        data-cart-id='{$cartId}'
                                    >Remove</button>
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
            async function sendCartAction(formData) {
                try {
                    const response = await fetch("updateCart.php", {
                        method: "POST",
                        body: formData,
                        headers: {
                            "Accept": "application/json"
                        }
                    });

                    const text = await response.text();
                    let data;

                    try {
                        data = JSON.parse(text);
                    } catch {
                        console.error("Invalid server response:", text);
                        alert("The server returned an invalid response. Check updateCart.php.");
                        return;
                    }
                    if (data.status === "success") {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert(data.message || "Cart operation failed.");
                    }
                } catch (error) {
                    console.error("Cart request failed:", error);
                    alert("Could not contact the server. Please try again.");
                }
            }

            document.querySelectorAll(".updateBtn").forEach(button => {
                button.addEventListener("click", function () {
                    const row = this.closest("tr");
                    const quantityInput = row.querySelector(".qtyInput");
                    const quantity = parseInt(quantityInput.value, 10);

                    if (!Number.isInteger(quantity) || quantity < 1) {
                        alert("Please enter a quantity of at least 1.");
                        return;
                    }

                    const formData = new FormData();
                    formData.append("cart_id", this.dataset.cartId);
                    formData.append("quantity", quantity);

                    sendCartAction(formData);
                });
            });

            document.querySelectorAll(".delBtn").forEach(button => {
                button.addEventListener("click", function () {
                    if (!confirm("Are you sure you want to remove this book?")) {
                        return;
                    }

                    const formData = new FormData();
                    formData.append("cart_id", this.dataset.cartId);
                    formData.append("remove", "1");

                    sendCartAction(formData);
                });
            });
        </script>


    </body>
</html>
