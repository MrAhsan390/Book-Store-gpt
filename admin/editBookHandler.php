admin/editBookHandler.php – Update Book via AJAX
<?php
session_start();
include("../includes/attach.php");

$response = ['status'=>'error','message'=>'Invalid request'];

// Admin check
if(!isset($_SESSION['userid']) || $_SESSION['role']!=='admin'){
    echo json_encode($response);
    exit;
}

if(isset($_POST['bk_id'], $_POST['bk_title'], $_POST['bk_author'], $_POST['bk_edition'], $_POST['bk_price'])){
    $id = intval($_POST['bk_id']);
    $title = $conn->real_escape_string($_POST['bk_title']);
    $author = $conn->real_escape_string($_POST['bk_author']);
    $edition = $conn->real_escape_string($_POST['bk_edition']);
    $price = floatval($_POST['bk_price']);

    // Check if new image uploaded
    if(isset($_FILES['bk_image']) && $_FILES['bk_image']['error']==0){
        $imgName = $_FILES['bk_image']['name'];
        $imgTmp = $_FILES['bk_image']['tmp_name'];
        $ext = pathinfo($imgName, PATHINFO_EXTENSION);
        $newName = time()."_".rand(1000,9999).".".$ext;
        $target = "../assets/uploads/books/".$newName;
        if(move_uploaded_file($imgTmp,$target)){
            // Update with new image
            $stmt = $conn->prepare("UPDATE books SET bk_title=?, bk_author=?, bk_eddition=?, bk_price=?, bk_image=? WHERE bk_id=?");
            $stmt->bind_param("ssdssi",$title,$author,$edition,$price,$newName,$id);
        } else {
            $response['message'] = "Image upload failed!";
            echo json_encode($response);
            exit;
        }
    } else {
        // Update without changing image
        $stmt = $conn->prepare("UPDATE books SET bk_title=?, bk_author=?, bk_eddition=?, bk_price=? WHERE bk_id=?");
        $stmt->bind_param("ssdsi",$title,$author,$edition,$price,$id);
    }

    if($stmt->execute()){
        $response['status'] = 'success';
        $response['message'] = "Book updated successfully!";
    } else {
        $response['message'] = "Database error: ".$conn->error;
    }
}

echo json_encode($response);
