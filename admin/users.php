admin/users.php – Manage Users (Admin)
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
    <title>Manage Users - Admin</title>
<style>
    body { 
        font-family: Arial, sans-serif; 
        background:#f4f4f4; 
        margin:0; 
        padding:0;
    }
   
    .container { 
        max-width: 900px; 
        margin: 30px auto; 
        background: #fff; 
        padding:20px; 
        border-radius:10px; 
        box-shadow:0 4px 10px rgba(0,0,0,0.1);
    }
   
    h2 { 
        color:#2b6cb0; 
        text-align:center; 
        margin-bottom:20px;
    }
   
    table { 
        width:100%; 
        border-collapse: collapse;
    }
   
    th, td { 
        border:1px solid #ccc; 
        padding:10px; 
        text-align:center;
    }
   
    th { 
        background:#2b6cb0; 
        color:white;
    }
   
    button { 
        padding:6px 12px; 
        border:none; 
        border-radius:5px; 
        cursor:pointer;
    }
   
    .delBtn { 
        background:#e53e3e; 
        color:white; 
    }
   
    .delBtn:hover { 
        background:#c53030; 
    }
   
   </style>
</head>
<body>

    <div class="container">
    <h2>Manage Users</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>

        <?php
        $users = $conn->query("SELECT * FROM users ORDER BY userid DESC");
        if($users->num_rows > 0){
            while($u = $users->fetch_assoc()){
                echo "<tr>";
                echo "<td>".$u['userid']."</td>";
                echo "<td>".$u['username']."</td>";
                echo "<td>".$u['email']."</td>";
                echo "<td>
                        <button class='delBtn' data-id='".$u['userid']."'>Delete</button>
                    </td>";
                echo "</tr>";
            }
        }else{
            echo "<tr><td colspan='4'>No users found.</td></tr>";
        }
        ?>
    </table>
    </div>

    <script>
    // Delete user
    document.querySelectorAll(".delBtn").forEach(btn=>{
        btn.onclick = ()=>{
            if(confirm("Are you sure you want to delete this user?")){
                let id = btn.dataset.id;
                fetch("delUser.php?id="+id)
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
