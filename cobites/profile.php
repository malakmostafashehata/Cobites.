<?php
require "auth.php";

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}require 'db.php';


$user_id = $_SESSION['user_id'];

$result = mysqli_query($conn,"SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($result);
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">    <style>
 :root{
    --brand-orange:#ff6b35;
    --brand-green:#102f15;
    --brand-soft:#eaf1b1;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Plus Jakarta Sans',sans-serif;
    background:var(--brand-green);
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    flex-direction:column;
    color:white;
    padding-top:120px;
}

nav{
    position:fixed;
    top:0;
    width:100%;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:18px 8%;
    background:var(--brand-green);
    border-bottom:1px solid rgba(255,255,255,0.1);
    z-index:1000;
}

.btn-home-nav{
    display:flex;
    align-items:center;
    gap:10px;
    color:white;
    text-decoration:none;
    font-weight:700;
    font-size:0.95rem;
    border:2px solid var(--brand-orange);
    padding:10px 22px;
    border-radius:50px;
    transition:.3s;
}

.btn-home-nav:hover{
    background:var(--brand-orange);
}
.profile-card{
    width:480px;  
    background:var(--brand-soft);
    color:#111;
    padding:35px;
    border-radius:26px;
    box-shadow:0 18px 35px rgba(0,0,0,0.25);
    text-align:center;
    margin-top:20px;
}

.avatar{
    width:75px;
    height:75px;
    margin:0 auto;
    background:linear-gradient(135deg,var(--brand-orange),#e85a28);
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:28px;
    font-weight:800;
    color:white;
}

h2{
    margin:15px 0 20px;
    font-size:1.3rem;
}

.info-grid{
    display:flex;
    flex-direction:column;
    gap:10px;
    margin-bottom:20px;
}

.info-item{
    background:white;
    padding:12px;
    border-radius:12px;
    text-align:left;
    border:1px solid #eee;
}

.info-item span{
    font-size:11px;
    color:#666;
}

.info-item p{
    margin-top:4px;
    font-weight:600;
    color:#111;
    font-size:14px;
}

.actions{
    display:flex;
    flex-direction:column;
    gap:10px;
}

.btn{
    display:block;
    padding:12px;
    border-radius:12px;
    font-weight:700;
    text-decoration:none;
    text-align:center;
    transition:.3s;
    font-size:14px;
}

.btn-primary{
    background:var(--brand-orange);
    color:white;
}

.btn-primary:hover{
    background:#e85a28;
}

.btn-secondary{
    background:white;
    color:#111;
    border:1px solid #ddd;
}

.btn-secondary:hover{
    background:#f2f2f2;
}

.btn-outline{
    background:transparent;
    border:1px solid var(--brand-orange);
    color:var(--brand-orange);
}

.btn-outline:hover{
    background:var(--brand-orange);
    color:white;
}

.toast{
    position:fixed;
    bottom:30px;
    left:50%;
    transform:translateX(-50%);
    background:var(--brand-orange);
    color:white;
    padding:12px 20px;
    border-radius:10px;
    font-weight:600;
    box-shadow:0 10px 30px rgba(0,0,0,0.2);
    animation:fadeIn .3s ease;
    z-index:9999;
}

@keyframes fadeIn{
    from{
        opacity:0;
        transform:translateX(-50%) translateY(10px);
    }
    to{
        opacity:1;
        transform:translateX(-50%) translateY(0);
    }
}
   </style>
</head>
<body>
        <nav>
    <div></div>

    <a href="homepage.php" class="btn-home-nav">
        <i class="fa-solid fa-arrow-left"></i>
        Back to Home
    </a>
</nav>
<div class="profile-card">

<div class="avatar-container">
    <div class="avatar">
        <?= strtoupper(substr($user['full_name'],0,1)); ?>
    </div>
    <div class="status-indicator"></div>
</div>

<h2><?= htmlspecialchars($user['full_name']); ?></h2>


<div class="info-grid">

<div class="info-item">
<span>Email</span>
<p><?= htmlspecialchars($user['email']); ?></p>
</div>

<div class="info-item">
<span>Phone</span>
<p><?= htmlspecialchars($user['phone'] ?? 'Not Set'); ?></p>
</div>

<div class="info-item">
<span>Address</span>
<p><?= htmlspecialchars($user['address'] ?? 'Not Set'); ?></p>
</div>

</div>

<div class="actions">

<a href="edit_profile.php" class="btn btn-primary">
✏ Edit Profile
</a>

<a href="change_password.php" class="btn btn-secondary">
🔐 Change Password
</a>

<a href="logout.php" class="btn btn-outline">
Sign Out
</a>
</div>
</div>
<?php if (isset($_SESSION['toast'])): ?>
<div id="toast" class="toast">
    <?= $_SESSION['toast']; ?>
</div>
<script>
setTimeout(() => {
    document.getElementById("toast").style.display = "none";
}, 3000);
</script>
<?php unset($_SESSION['toast']); endif; ?>
</body>
</html>