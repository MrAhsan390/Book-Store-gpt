admin/booksShow.php – Show All Books (Admin)
<?php
session_start();
include("../includes/attach.php");
include("../includes/header.php");

// Admin check
if(!isset($_SESSION['userid']) || $_SESSION['role']!=='admin'){
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Manage Books - Admin</title>
<style>
body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:0;}
.container { max-width: 1000px; margin: 30px auto; background: #fff; padding:20px; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1);}
h2 { color:#2b6cb0; text-align:center; margin-bottom:20px;}
table { width:100%; border-collapse: collapse;}
th, td { border:1px solid #ccc; padding:10px; text-align:center;}
th { background:#2b6cb0; color:white;}
button { padding:6px 12px; border:none; border-radius:5px; cursor:pointer;}
.editBtn { background:#38a169; color:white; }
.editBtn:hover { background:#2f855a; }
.delBtn { background:#e53e3e; color:white; }
.delBtn:hover { background:#c53030; }

/* Modal */
.modal { display:none; position:fixed; z-index:999; left:0; top:0; width:100%; height:100%; overflow:auto; background: rgba(0,0,0,0.5);}
.modal-content { background:#fff; margin:10% auto; padding:20px; border-radius:10px; width:400px; position:relative;}
.close { position:absolute; top:10px; right:15px; font-size:20px; cursor:pointer;}
input, button { width:100%; padding:10px; margin:8px 0; border-radius:5px; border:1px solid #ccc;}
button { background:#2b6cb0; color:white; }
button:hover { background:#1e4f8a; }
</style>
</head>
<body>

<div class="container">
<h2>Manage Books</h2>
<table>
<tr>
<th>ID</th>
<th>Title</th>
<th>Author</th>
<th>Edition</th>
<th>Price</th>
<th>Image</th>
<th>Actions</th>
</tr>

<?php
$books = $conn->query("SELECT * FROM books ORDER BY bk_id DESC");
if($books->num_rows > 0){
    while($b = $books->fetch_assoc()){
        echo "<tr>";
        echo "<td>".$b['bk_id']."</td>";
        echo "<td>".$b['bk_title']."</td>";
        echo "<td>".$b['bk_author']."</td>";
        echo "<td>".$b['bk_eddition']."</td>";
        echo "<td>$".$b['bk_price']."</td>";
        echo "<td><img src='../assets/uploads/books/".$b['bk_image']."' width='50'></td>";
        echo "<td>
                <button class='editBtn' data-id='".$b['bk_id']."'>Edit</button>
                <button class='delBtn' data-id='".$b['bk_id']."'>Delete</button>
              </td>";
        echo "</tr>";
    }
}else{
    echo "<tr><td colspan='7'>No books found.</td></tr>";
}
?>
</table>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
<div class="modal-content">
<span class="close" id="closeEdit">&times;</span>
<h3>Edit Book</h3>
<form id="editForm" enctype="multipart/form-data">
<input type="hidden" name="bk_id" id="edit_id">
<input type="text" name="bk_title" id="edit_title" placeholder="Book Title" required>
<input type="text" name="bk_author" id="edit_author" placeholder="Author" required>
<input type="text" name="bk_edition" id="edit_edition" placeholder="Edition" required>
<input type="number" name="bk_price" id="edit_price" placeholder="Price" required step="0.01">
<input type="file" name="bk_image" id="edit_image" accept="image/*">
<button type="submit">Update Book</button>
<div id="editMsg"></div>
</form>
</div>
</div>

<script>
// Edit modal
let editModal = document.getElementById("editModal");
let closeEdit = document.getElementById("closeEdit");
closeEdit.onclick = ()=>{ editModal.style.display="none"; }

document.querySelectorAll(".editBtn").forEach(btn=>{
    btn.onclick = ()=>{
        let id = btn.dataset.id;
        fetch("getBook.php?id="+id)
        .then(res=>res.json())
        .then(data=>{
            document.getElementById("edit_id").value = data.bk_id;
            document.getElementById("edit_title").value = data.bk_title;
            document.getElementById("edit_author").value = data.bk_author;
            document.getElementById("edit_edition").value = data.bk_eddition;
            document.getElementById("edit_price").value = data.bk_price;
            editModal.style.display = "block";
        });
    }
});

// AJAX update
document.getElementById("editForm").onsubmit = function(e){
    e.preventDefault();
    let fd = new FormData(this);
    fetch("editBookHandler.php",{
        method:"POST",
        body: fd
    }).then(res=>res.json())
      .then(data=>{
        document.getElementById("editMsg").innerText = data.message;
        document.getElementById("editMsg").style.color = data.status=="success"?"green":"red";
        if(data.status=="success"){ setTimeout(()=>{ location.reload(); }, 1000);}
      });
};

// Delete
document.querySelectorAll(".delBtn").forEach(btn=>{
    btn.onclick = ()=>{
        if(confirm("Are you sure you want to delete this book?")){
            let id = btn.dataset.id;
            fetch("delBook.php?id="+id)
            .then(res=>res.json())
            .then(data=>{
                alert(data.message);
                if(data.status=="success"){ location.reload(); }
            });
        }
    }
});
</script>

</body>
</html>

