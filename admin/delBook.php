admin/delBook.php – Delete Book (AJAX)
<?php
session_start();
include("../includes/attach.php");

$response = ['status'=>'error','message'=>'Invalid request'];

// Admin check
if(!isset($_SESSION['userid']) || $_SESSION['role']!=='admin'){
    echo json_encode($response);
    exit;
}

if(isset($_GET['id'])){
    $id = intval($_GET['id']);

    // Get book image to delete from server
    $res = $conn->query("SELECT bk_image FROM books WHERE bk_id=$id");
    if($res->num_rows > 0){
        $book = $res->fetch_assoc();
        $imagePath = "../assets/uploads/books/".$book['bk_image'];
        if(file_exists($imagePath)) unlink($imagePath);

        // Delete book record
        if($conn->query("DELETE FROM books WHERE bk_id=$id")){
            $response['status'] = 'success';
            $response['message'] = "Book deleted successfully!";
        } else {
            $response['message'] = "Database error: ".$conn->error;
        }
    } else {
        $response['message'] = "Book not found!";
    }
}

echo json_encode($response);

