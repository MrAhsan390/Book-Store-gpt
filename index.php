
<!-- index.php – Homepage -->
<?php
session_start();
include("includes/attach.php");
include("includes/header.php");
?>


    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #2b6cb0;
        }
        .books-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .book-card {
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            width: 220px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .book-card img {
            width: 150px;
            height: 200px;
            object-fit: cover;
            margin-bottom: 10px;
        }
        .book-card h3 {
            font-size: 18px;
            margin: 5px 0;
        }
        .book-card p {
            margin: 5px 0;
            color: #333;
        }
        .book-card button {
            background: #2b6cb0;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }
        .book-card button:hover {
            background: #1e4f8a;
        }
        </style>
    </head>
    <section>

        <div class="container">
        <h1>Welcome to the Online Bookstore</h1>
        <div class="books-grid">
        <?php
        // Fetch latest books from database
        $result = $conn->query("SELECT * FROM books ORDER BY bk_id DESC LIMIT 8");
        if($result->num_rows > 0){
            while($book = $result->fetch_assoc()){
                $imgPath = "assets/uploads/books/".$book['bk_image'];
                if(!file_exists($imgPath)) $imgPath = "assets/images/no-image.png";
                echo '<div class="book-card">';
                echo '<img src="'.$imgPath.'" alt="'.htmlspecialchars($book['bk_title']).'">';
                echo '<h3>'.htmlspecialchars($book['bk_title']).'</h3>';
                echo '<p>Author: '.htmlspecialchars($book['bk_author']).'</p>';
                echo '<p>Price: $'.$book['bk_price'].'</p>';
                echo '<a href="books/bookDetail.php?bk_id='.$book['bk_id'].'"><button>View Details</button></a>';
                echo '</div>';
            }
        }else{
            echo "<p>No books available right now.</p>";
        }
        ?>
        </div>
        </div>

</section>
        <?php include("includes/footer.php"); ?>

