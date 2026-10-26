<?php
    session_start();
    require_once __DIR__ . "/includes/attach.php";

    if (!isset($_SESSION['userid'])) {
        header("Location: login.php");
        exit;
    }

    $user_id = (int) $_SESSION['userid'];

    // Fetch user information
    $stmt = $conn->prepare(
        "SELECT username, email FROM users WHERE userid = ?"
    );
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$user) {
        session_destroy();
        header("Location: login.php");
        exit;
    }

    // Fetch orders belonging to this user
    $orderStmt = $conn->prepare(
        "SELECT order_id, total_amount, order_status, order_date
        FROM orders
        WHERE userid = ?
        ORDER BY order_id DESC"
    );
    $orderStmt->bind_param("i", $user_id);
    $orderStmt->execute();
    $orderResult = $orderStmt->get_result();

    include __DIR__ . "/includes/header.php";
?>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f4f4;
        margin: 0;
        padding: 0;
    }
    .container {
        max-width: 900px;
        margin: 30px auto;
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        box-sizing: border-box;
    }
    h2 {
        color: #2b6cb0;
        margin-bottom: 20px;
    }
    form input,
    form button {
        width: 100%;
        padding: 10px;
        margin: 8px 0;
        border-radius: 5px;
        border: 1px solid #ccc;
        box-sizing: border-box;
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
    form button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    #msg {
        margin-top: 10px;
        font-weight: bold;
    }
    .table-wrapper {
        width: 100%;
        overflow-x: auto;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
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
    @media (max-width: 600px) {
        .container {
            margin: 15px;
            padding: 15px;
        }
        th, td {
            padding: 8px;
            font-size: 13px;
        }
    }
</style>

<section>
    <div class="container">
        <h2>My Profile</h2>

        <form id="profileForm">
            <label for="username">Username</label>
            <input
                type="text"
                id="username"
                name="username"
                value="<?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>"
                required
            >

            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>"
                required
            >

            <label for="password">New Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Leave blank to keep current password"
                autocomplete="new-password"
            >

            <button type="submit" id="updateBtn">Update Profile</button>
        </form>

        <div id="msg" role="status" aria-live="polite"></div>

        <h2>My Orders</h2>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Books</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($orderResult->num_rows > 0): ?>
                    <?php while ($order = $orderResult->fetch_assoc()): ?>
                        <?php
                        // Get this order's books through order_items
                        $bookStmt = $conn->prepare(
                            "SELECT b.bk_title
                             FROM order_items oi
                             JOIN books b ON b.bk_id = oi.bk_id
                             WHERE oi.order_id = ?
                             ORDER BY b.bk_title"
                        );

                        $bookStmt->bind_param("i", $order['order_id']);
                        $bookStmt->execute();
                        $bookResult = $bookStmt->get_result();

                        $bookTitles = [];
                        while ($book = $bookResult->fetch_assoc()) {
                            $bookTitles[] = $book['bk_title'];
                        }
                        $bookStmt->close();

                        $titles = $bookTitles
                            ? implode(", ", $bookTitles)
                            : "No book details";
                        ?>

                        <tr>
                            <td><?= (int) $order['order_id'] ?></td>
                            <td><?= htmlspecialchars($titles, ENT_QUOTES, 'UTF-8') ?></td>
                            <td>$<?= number_format((float) $order['total_amount'], 2) ?></td>
                            <td><?= htmlspecialchars($order['order_status'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($order['order_date'], ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No orders yet.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<script>
document.getElementById("profileForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    const form = this;
    const button = document.getElementById("updateBtn");
    const msg = document.getElementById("msg");

    button.disabled = true;
    button.textContent = "Updating...";
    msg.textContent = "";

    try {
        const response = await fetch("updateProfile.php", {
            method: "POST",
            body: new FormData(form),
            headers: {
                "Accept": "application/json"
            }
        });

        const responseText = await response.text();
        console.log("Server response:", responseText);

        let data;

        try {
            data = JSON.parse(responseText);
        } catch {
            throw new Error(
                "The server returned an invalid response. Check the browser console."
            );
        }

        msg.textContent = data.message || "Request completed.";
        msg.style.color = data.status === "success" ? "green" : "red";

        if (data.status === "success") {
            document.getElementById("password").value = "";
        }
    } catch (error) {
        msg.textContent = error.message;
        msg.style.color = "red";
        console.error("Profile update error:", error);
    } finally {
        button.disabled = false;
        button.textContent = "Update Profile";
    }
});
</script>
<?php
$orderStmt->close();
include __DIR__ . "/includes/footer.php";
?>
