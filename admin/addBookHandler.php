<!-- admin/addBookHandler.php – AJAX handler for adding book -->
<?php

    session_start();
    include("../includes/attach.php");

    $response = ['status'=>'error','message'=>'Invalid request'];

    // Admin check
    if(!isset($_SESSION['userid']) || $_SESSION['role']!=='admin'){
        echo json_encode($response);
        exit;
    }

    if(isset($_POST['bk_title'], $_POST['bk_author'], $_POST['bk_edition'], $_POST['bk_price']) && isset($_FILES['bk_image'])){
        $title = $conn->real_escape_string($_POST['bk_title']);
        $author = $conn->real_escape_string($_POST['bk_author']);
        $edition = $conn->real_escape_string($_POST['bk_edition']);
        $price = floatval($_POST['bk_price']);

        // Handle image upload
        $imgName = $_FILES['bk_image']['name'];
        $imgTmp = $_FILES['bk_image']['tmp_name'];
        $ext = pathinfo($imgName, PATHINFO_EXTENSION);
        $newName = time()."_".rand(1000,9999).".".$ext;
        $target = "../assets/uploads/books/".$newName;

        if(move_uploaded_file($imgTmp,$target)){
            $stmt = $conn->prepare("INSERT INTO books (bk_title,bk_author,bk_eddition,bk_price,bk_image) VALUES (?,?,?,?,?)");
            $stmt->bind_param("ssdss",$title,$author,$edition,$price,$newName);
            if($stmt->execute()){
                $response['status'] = 'success';
                $response['message'] = "Book added successfully!";
            } else {
                $response['message'] = "Database error: ".$conn->error;
            }
        } else {
            $response['message'] = "Image upload failed!";
        }
    }

    echo json_encode($response);
?>