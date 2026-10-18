<?php
session_start();
include_once("../includes/attach.php");
include_once("../includes/header.php");
?>

<style>
    body { 
        font-family: Arial, sans-serif; 
        background:#f4f4f4; 
        margin:0; 
        padding:0;
    }

    .container { 
        max-width:1200px; 
        margin:30px auto; 
        padding:20px; 
    }
    
    h2 { 
        text-align:center; 
        color:#2b6cb0; 
        margin-bottom:20px; 
    }
    
    .search-bar { 
        text-align:center; 
        margin-bottom:20px; 
    }
    
    .search-bar input { 
        width:300px; 
        padding:10px; 
        border-radius:5px; 
        border:1px solid #ccc; 
    }
    
    .search-bar button { 
        padding:10px 15px; 
        border:none; 
        border-radius:5px; 
        background:#2b6cb0; 
        color:white; 
        cursor:pointer; 
    }
    
    .search-bar button:hover { 
        background:#1e4f8a; 
    }

    .books-grid { 
        display:grid; 
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); 
        gap:20px; 
    }
    
    .book-card { 
        background:#fff; 
        border-radius:10px; 
        padding:15px; 
        text-align:center; 
        box-shadow:0 4px 10px rgba(0,0,0,0.1);
    }
    
    .book-card img { 
        max-width:100%; 
        height:200px; 
        object-fit:cover; 
        border-radius:5px; 
    }
    
    .book-card h3 { 
        color:#2b6cb0; 
        margin:10px 0 5px; 
    }
    
    .book-card p { 
        margin:5px 0; 
    }
    
    .book-card button { 
        padding:6px 12px; 
        border:none; 
        border-radius:5px; 
        cursor:pointer; 
        margin-top:5px; 
    }
    
    .detailsBtn { 
        background:#38a169; 
        color:white; 
    }
    
    .detailsBtn:hover { 
        background:#2f855a; 
    }
    
    .cartBtn { 
        background:#2b6cb0; 
        color:white; 
    }
    
    .cartBtn:hover { 
        background:#1e4f8a; 
    }
    
    #msg { 
        text-align:center;
        margin-bottom:15px; 
        font-weight:bold; 
        }

</style>
</head>
<section>

    <div class="container">
        <h2>Book Catalog</h2>

        <div id="msg"></div>

        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search books by title or author">
            <button id="searchBtn">Search</button>
        </div>

        <div class="books-grid" id="booksGrid">

            <?php
                $books = $conn->query("SELECT * FROM books ORDER BY bk_id DESC");
                if($books->num_rows > 0){
                    while($b = $books->fetch_assoc()){
                        echo "<div class='book-card'>";
                        echo "<img src='../assets/uploads/books/".$b['bk_image']."'>";
                        echo "<h3>".$b['bk_title']."</h3>";
                        echo "<p>Author: ".$b['bk_author']."</p>";
                        echo "<p>Edition: ".$b['bk_eddition']."</p>";
                        echo "<p>Price: $".$b['bk_price']."</p>";
                        echo "<button class='detailsBtn' onclick='window.location=\"bookDetail.php?id=".$b['bk_id']."\"'>View Details</button>";
                        echo "<button class='cartBtn' onclick='window.location=\".cart/addToCart.php?data-id=".$b['bk_id']."\"'>Add to Cart</button>";
                        echo "</div>";
                    }
                }else{
                    echo "<p>No books available.</p>";
                }
            ?>

        </div>
    </div>

    <script>
    // Live search
        document.getElementById("searchBtn").onclick = function(){
            let query = document.getElementById("searchInput").value;
            fetch("searchBooks.php?q="+encodeURIComponent(query))
            .then(res=>res.text())
            .then(html=>{
                document.getElementById("booksGrid").innerHTML = html;
            });
        }

        // Add to Cart
        document.querySelectorAll(".cartBtn").forEach(btn=>{
            btn.onclick = ()=>{
                let id = btn.dataset.id;
                fetch("../cart/addToCart.php?id="+id)
                .then(res=>res.json())
                .then(data=>{
                    let msg = document.getElementById("msg");
                    msg.innerText = data.message;
                    msg.style.color = data.status=="success"?"green":"red";
                });
            }
        });
    </script>

</section>

<?php 
include("./includes/footer.php");
?>