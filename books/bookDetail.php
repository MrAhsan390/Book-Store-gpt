books/bookDetails.php – Book Details Page
<?php
    session_start();
    include("../includes/attach.php");
    include("../includes/header.php");

    if(!isset($_GET['id'])){
        echo "Invalid book ID.";
        exit;
    }

    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM books WHERE bk_id=$id");

    if($result->num_rows == 0){
        echo "Book not found.";
        exit;
    }

    $book = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo $book['bk_title']; ?> - Book Details</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:0;}
        .container { max-width:800px; margin:50px auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1);}
        h2 { color:#2b6cb0; margin-bottom:20px; }
        .book-img { max-width:300px; float:left; margin-right:20px; border-radius:10px; }
        .details { overflow:hidden; }
        .details p { margin:10px 0; }
        button { padding:10px 15px; border:none; border-radius:5px; cursor:pointer; background:#2b6cb0; color:white; margin-top:10px; }
        button:hover { background:#1e4f8a; }
        #msg { font-weight:bold; margin-top:10px; }
        </style>
</head>
<body>

    <div class="container">
    <h2><?php echo $book['bk_title']; ?></h2>
    <div class="details">
    <img src="../assets/uploads/books/<?php echo $book['bk_image']; ?>" class="book-img">
    <p><strong>Author:</strong> <?php echo $book['bk_author']; ?></p>
    <p><strong>Edition:</strong> <?php echo $book['bk_eddition']; ?></p>
    <p><strong>Price:</strong> $<?php echo $book['bk_price']; ?></p>
    <p><strong>Description:</strong> <?php echo isset($book['bk_description'])?$book['bk_description']:"No description available."; ?></p>
    <button id="addCartBtn" data-id="<?php echo $book['bk_id']; ?>">Add to Cart</button>
    <div id="msg"></div>
    </div>
    </div>

    <script>
        // Add to Cart
        document.getElementById("addCartBtn").onclick = function(){
            let id = this.dataset.id;
            fetch("../cart/addToCart.php?id="+id)
            .then(res=>res.json())
            .then(data=>{
                let msg = document.getElementById("msg");
                msg.innerText = data.message;
                msg.style.color = data.status=="success"?"green":"red";
            });
        };
    </script>

</body>
</html>

