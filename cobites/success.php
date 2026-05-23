<?php
include "db.php";
require "auth.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$name = $_SESSION['full_name'] ?? 'Hero';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success | Cobites</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #102f15; 
            color: white; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            height: 100vh; 
            margin: 0; 
            text-align: center; 
        }
        .card { 
            background: #eaf1b1; 
            color: #102f15; 
            padding: 50px; 
            border-radius: 40px; 
            box-shadow: 0 20px 50px rgba(0,0,0,0.4); 
            max-width: 500px;
            width: 90%;
        }
        .icon-circle {
            width: 80px;
            height: 80px;
            background: #ff6b35;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 20px;
        }
        h1 { color: #ff6b35; margin-bottom: 10px; font-size: 2rem; }
        p { font-size: 1.1rem; opacity: 0.9; margin-bottom: 30px; }
        .btn { 
            display: inline-block; 
            padding: 15px 30px; 
            margin: 10px; 
            border-radius: 50px; 
            text-decoration: none; 
            font-weight: 800; 
            background: #ff6b35; 
            color: white; 
            transition: 0.3s;
        }
        .btn:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(255, 107, 53, 0.3); }
        .btn-outline { background: transparent; border: 2px solid #102f15; color: #102f15; }
        .btn-outline:hover { background: #102f15; color: #eaf1b1; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-circle">
            <i class="fas fa-check"></i>
        </div>
        <h1>Submission Successful!</h1>
        <p>Thanks for being a hero, <strong><?php echo htmlspecialchars($name); ?></strong>! Your donation is now live for volunteers to see.</p>
        
        <a href="volunteer.php" class="btn">Donate More</a>
        <a href="history.php" class="btn btn-outline">My History</a>
    </div>
</body>
</html>