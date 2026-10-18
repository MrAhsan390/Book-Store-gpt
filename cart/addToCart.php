cart/addToCart.php – Add Book to Cart
<?php
session_start();
include("../includes/attach.php");

$response = ['status'=>'error','message'=>'Please login first.'];

// User must be logged in
if(isset($_SESSION['userid'])){
    $userid = $_SESSION['userid'];

    if(isset($_GET['id'])){
        $book_id = intval($_GET['id']);

        // Check if book exists
        $res = $conn->query("SELECT * FROM books WHERE bk_id=$book_id");
        if($res->num_rows == 0){
            $response['message'] = "Book not found.";
        } else {
            // Check if already in cart
            $check = $conn->query("SELECT * FROM cart WHERE user_id=$userid AND bk_id=$book_id");
            if($check->num_rows > 0){
                // Increment quantity
                $conn->query("UPDATE cart SET quantity=quantity+1 WHERE user_id=$userid AND bk_id=$book_id");
                $response['status'] = "success";
                $response['message'] = "Book quantity updated in cart!";
            } else {
                // Add new entry
                $stmt = $conn->prepare("INSERT INTO cart(user_id, bk_id, quantity) VALUES(?,?,1)");
                $stmt->bind_param("ii",$userid,$book_id);
                if($stmt->execute()){
                    $response['status'] = "success";
                    $response['message'] = "Book added to cart!";
                } else {
                    $response['message'] = "Database error: ".$conn->error;
                }
            }
        }
    } else {
        $response['message'] = "Invalid book ID.";
    }
}

echo json_encode($response);

