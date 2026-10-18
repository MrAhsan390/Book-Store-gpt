<?php
session_start();
include("includes/attach.php");

$response = ['status'=>'error','message'=>'Invalid request'];

if(isset($_POST['email']) && isset($_POST['password'])){
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    if($result->num_rows == 1){
        $user = $result->fetch_assoc();
        if(password_verify($password, $user['password'])){
            $_SESSION['userid'] = $user['userid'];
            $_SESSION['username'] = $user['username'];
            $response['status'] = 'success';
            $response['message'] = "Login successful!";
        } else {
            $response['message'] = "Incorrect password!";
        }
    } else {
        $response['message'] = "Email not found!";
    }
}

echo json_encode($response);

