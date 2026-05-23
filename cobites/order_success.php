<?php
require "auth.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order Success | Cobites</title>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;800&display=swap" rel="stylesheet">

<style>

body{
    font-family:'Plus Jakarta Sans',sans-serif;
    background:#102f15;
    color:white;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    margin:0;
}

.card{
    background: #eaf1b1;
    color: #102f15;
    padding:50px;
    border-radius:30px;
    text-align:center;
    box-shadow:0 20px 50px rgba(0,0,0,0.3);
}

.btn{
    display:inline-block;
    padding:15px 30px;
    margin:10px;
    border-radius:50px;
    text-decoration:none;
    font-weight:800;
    background:#ff6b35;
    color:white;
}

.secondary{
    background:transparent;
    border:2px solid #102f15;
    color:#102f15;
}

</style>
</head>
<body>

<div class="card">

<h1 style="color:#ff6b35;">
Order Successful!
</h1>

<p>
Thank you, <?= htmlspecialchars($_SESSION['full_name']) ?>.
</p>

<br>

<a href="charity.php" class="btn">
Order More
</a>

<a href="charity_history.php" class="btn secondary">
My Orders
</a>

</div>

</body>
</html>