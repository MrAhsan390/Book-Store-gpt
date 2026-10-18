<?php
session_start();
include("includes/attach.php");

$response = ['status'=>'error','message'=>'Invalid request'];

if(isset($_POST['username'], $_POST['email'], $_POST['password'], $_POST['confirm_password'])){
    
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if($password !== $confirm_password){
        $response['message'] = "Passwords do not match!";
        echo json_encode($response);
        exit;
    }

    // Check if email already exists
    $check = $conn->query("SELECT userid FROM users WHERE email='$email'");
    if($check->num_rows > 0){
        $response['message'] = "Email already registered!";
        echo json_encode($response);
        exit;
    }

    // Insert user
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username,email,password) VALUES (?,?,?)");
    $stmt->bind_param("sss",$username,$email,$hash);

    if($stmt->execute()){
        $response['status'] = 'success';
        $response['message'] = "Registration successful! Redirecting to login...";
    } else {
        $response['message'] = "Database error: ".$conn->error;
    }
}

echo json_encode($response);
