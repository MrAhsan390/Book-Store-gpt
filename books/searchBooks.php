books/searchBooks.php – Live Search for Books
<?php
session_start();
include("../includes/attach.php");

if(!isset($_GET['q'])) exit;

$query = $conn->real_escape_string($_GET['q']);

$sql = "SELECT * FROM books WHERE bk_title LIKE '%$query%' OR bk_author LIKE '%$query%' ORDER BY bk_id DESC";
$books = $conn->query($sql);

if($books->num_rows > 0){
    while($b = $books->fetch_assoc()){
        echo "<div class='book-card'>";
        echo "<img src='../assets/uploads/books/".$b['bk_image']."'>";
        echo "<h3>".$b['bk_title']."</h3>";
        echo "<p>Author: ".$b['bk_author']."</p>";
        echo "<p>Edition: ".$b['bk_eddition']."</p>";
        echo "<p>Price: $".$b['bk_price']."</p>";
        echo "<button class='detailsBtn' onclick='window.location=\"bookDetails.php?id=".$b['bk_id']."\"'>View Details</button>";
        echo "<button class='cartBtn' data-id='".$b['bk_id']."'>Add to Cart</button>";
        echo "</div>";
    }
}else{
    echo "<p>No books found for your search.</p>";
}
