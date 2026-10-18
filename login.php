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
<title>Login - Online Bookstore</title>
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
      
        <h2>Login</h2>
        <form id="loginForm">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
      
        <div id="msg"></div>
        <a href="register.php">Don't have an account? Register</a>
    </div>

    <script>
    document.getElementById("loginForm").onsubmit = function(e){
        e.preventDefault();
        let formData = new FormData(this);
        fetch("loginHandler.php",{
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            let msg = document.getElementById("msg");
            msg.innerText = data.message;
            msg.style.color = data.status=="success"?"green":"red";
            if(data.status=="success"){
                setTimeout(()=>{ window.location="profile.php"; }, 1000);
            }
        });
    }
    </script>

</body>
</html>
