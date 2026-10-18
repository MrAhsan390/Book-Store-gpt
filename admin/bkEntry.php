admin/bkEntry.php – Add New Book
<?php
session_start();
include("../includes/attach.php");
include("../includes/header.php");

// Admin check
if(!isset($_SESSION['userid']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../login.php");
    exit;
}
?>


<!DOCTYPE html>
<html>
    <head>
    <meta charset="UTF-8">
    <title>Add New Book - Online Bookstore</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin:0;
            padding:0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #2b6cb0;
            text-align: center;
            margin-bottom: 20px;
        }
        input, textarea, button {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background: #2b6cb0;
            color: white;
            border: none;
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
        </style>
    </head>
    <body>

        <div class="container">

            <h2>Add New Book</h2>

            <form id="bookForm" enctype="multipart/form-data">
                <input type="text" name="bk_title" placeholder="Book Title" required>
                <input type="text" name="bk_author" placeholder="Author" required>
                <input type="text" name="bk_edition" placeholder="Edition" required>
                <input type="number" name="bk_price" placeholder="Price" required step="0.01">
                <input type="file" name="bk_image" accept="image/*" required>
                <button type="submit">Add Book</button>
            </form>

            <div id="msg"></div>

        </div>

        <script>
            document.getElementById("bookForm").onsubmit = function(e){
                e.preventDefault();
                let formData = new FormData(this);
                fetch("addBookHandler.php",{
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    let msg = document.getElementById("msg");
                    msg.innerText = data.message;
                    msg.style.color = data.status=="success"?"green":"red";
                    if(data.status=="success"){
                        document.getElementById("bookForm").reset();
                    }
                });
            }
        </script>

    </body>
</html>
