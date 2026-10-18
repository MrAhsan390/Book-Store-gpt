<!-- register.php – User Registration -->
<?php
    session_start();
    include("includes/attach.php");

    // If already logged in, redirect to profile
    if(isset($_SESSION['userid'])){
        header("Location: profile.php");
        exit;
    }
?>

<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Register - Online Bookstore</title>
        <style>
             body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
            }
            .container {
                max-width: 400px;
                margin: 80px auto;
                background: #fff;
                padding: 25px;
                border-radius: 10px;
                box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            }
            h2 {
                text-align: center;
                color: #2b6cb0;
                margin-bottom: 20px;
            }
            input {
                width: 100%;
                padding: 10px;
                margin: 8px 0;
                border-radius: 5px;
                border: 1px solid #ccc;
            }
            button {
                width: 100%;
                padding: 10px;
                background: #2b6cb0;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }
            button:hover {
                background: #1e4f8a;
            }
            #msg {
                margin-top: 10px;
                font-weight: bold;
                text-align: center;
            }
            a {
                display: block;
                text-align: center;
                margin-top: 10px;
                color: #2b6cb0;
                text-decoration: none;
            }
            a:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>

        <div class="container">
            <h2>Register</h2>
            <form id="registerForm">
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit" >Register</button>
            </form>

            <div id="msg"></div>
        <a href="login.php">Already have an account? Login</a>
        </div>

        <script>
            document.getElementById("registerForm").onsubmit = function(e){
                e.preventDefault();
                let formData = new FormData(this);
                fetch("registerHandler.php",{
                    method: "POST",
                    body: formData
                })
                
                .then(res => res.json())
                .then(data => {
                    let msg = document.getElementById("msg");
                    msg.innerText = data.message;
                    msg.style.color = data.status=="success"?"green":"red";
                    if(data.status=="success"){
                        setTimeout(()=>{ window.location="login.php"; }, 1500);
                    }
                });
            }
        </script>

    </body>

</html>

<?php

if(isset($_POST["submit"])){
//   $username   = $_POST["username"];
//   $email      = $_POST["email"];
//   $password   = $_POST["password"];
//   $cpassword  = $_POST["confirm_possword"];


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

?>