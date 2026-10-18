<?php
session_start();
include("includes/attach.php");
include("includes/header.php");

// Redirect if not logged in
if(!isset($_SESSION['userid'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['userid'];

// Fetch user info
$result = $conn->query("SELECT * FROM users WHERE userid=$user_id");
$user = $result->fetch_assoc();
?>


<style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f4f4;
        margin:0;
        padding:0;
    }
    .container {
        max-width: 900px;
        margin: 30px auto;
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    h2 {
        color: #2b6cb0;
        margin-bottom: 20px;
    }
    form input, form button {
        width: 100%;
        padding: 10px;
        margin: 8px 0;
        border-radius: 5px;
        border: 1px solid #ccc;
    }
    form button {
        background: #2b6cb0;
        color: white;
        border: none;
        cursor: pointer;
    }
    form button:hover {
        background: #1e4f8a;
    }
    #msg {
        margin-top: 10px;
        font-weight: bold;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 30px;
    }
    table, th, td {
        border: 1px solid #ccc;
    }
    th, td {
        padding: 10px;
        text-align: center;
    }
    th {
        background: #2b6cb0;
        color: white;
    }
</style>
<section>
    <div class="container">

        <h2>My Profile</h2>
        <form id="profileForm">
            <label>Username</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            <label>New Password</label>
            <input type="password" name="password" placeholder="Leave blank to keep current">
            <button type="submit">Update Profile</button>
        </form>
    <div id="msg"></div>

    <h2>My Orders</h2>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Books</th>
            <th>Total</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
    <?php

        $orderResult = $conn->query("SELECT * FROM orders WHERE userid=$user_id ORDER BY order_id DESC");
        if($orderResult->num_rows > 0){
            while($order = $orderResult->fetch_assoc()){
                // Fetch book titles for this order
                $book_ids = explode(',', $order['book_ids']);
                $book_titles = [];
                foreach($book_ids as $bid){
                    $bres = $conn->query("SELECT bk_title FROM books WHERE bk_id=$bid");
                    if($bres->num_rows > 0) $book_titles[] = $bres->fetch_assoc()['bk_title'];
                }
                echo "<tr>";
                echo "<td>".$order['order_id']."</td>";
                echo "<td>".implode(", ", $book_titles)."</td>";
                echo "<td>$".$order['total']."</td>";
                echo "<td>".$order['status']."</td>";
                echo "<td>".$order['order_date']."</td>";
                echo "</tr>";
            }
        }else{
            echo "<tr><td colspan='5'>No orders yet.</td></tr>";
        }
    ?>
    </table>
    </div>

    <script>
        document.getElementById("profileForm").onsubmit = function(e){
            e.preventDefault();
            let formData = new FormData(this);
            fetch("updateProfile.php",{
                method:"POST",
                body: formData
            })
            .then(res=>res.json())
            .then(data=>{
                let msg = document.getElementById("msg");
                msg.innerText = data.message;
                msg.style.color = data.status=="success"?"green":"red";
            });
        }
    </script>

    </section>
    
    <?php include("includes/footer.php"); ?>

