

<!-- updateProfile.php – AJAX Profile Update Handler -->
<?php
session_start();
include("includes/attach.php");

$response = ['status'=>'error','message'=>'Invalid request'];

if(!isset($_SESSION['userid'])){
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['userid'];
$username = $conn->real_escape_string($_POST['username'] ?? '');
$email = $conn->real_escape_string($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if(!$username || !$email){
    $response['message'] = "Username and email are required!";
    echo json_encode($response);
    exit;
}

// Check email uniqueness
$result = $conn->query("SELECT userid FROM users WHERE email='$email' AND userid!=$user_id");
if($result->num_rows > 0){
    $response['message'] = "Email already taken!";
    echo json_encode($response);
    exit;
}

// Update query
if($password){
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET username=?, email=?, password=? WHERE userid=?");
    $stmt->bind_param("sssi",$username,$email,$hash,$user_id);
}else{
    $stmt = $conn->prepare("UPDATE users SET username=?, email=? WHERE userid=?");
    $stmt->bind_param("ssi",$username,$email,$user_id);
}

if($stmt->execute()){
    $response['status'] = 'success';
    $response['message'] = "Profile updated successfully!";
}else{
    $response['message'] = "Database error: ".$conn->error;
}

echo json_encode($response);
