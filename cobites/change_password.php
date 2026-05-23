<?php
require "auth.php";
if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}
$id = (int) $_SESSION['user_id'];

$oldErr = "";
$newErr = "";
$confirmErr = "";

function test_input($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
if($_SERVER['REQUEST_METHOD'] == "POST"){

    $old = test_input($_POST['old_password']);
    $new = test_input($_POST['new_password']);
    $confirm = test_input($_POST['confirm_password']);

    $result = mysqli_query($conn,"SELECT password FROM users WHERE id=$id");
    $user = mysqli_fetch_assoc($result);

    $valid = true;
    if(empty($old)){
        $oldErr = "Old password is required";
        $valid = false;
    }
    if(empty($new)){
        $newErr = "New password is required";
        $valid = false;
    }
    elseif(strlen($new) <= 6){
        $newErr = "Password must be at least 7 characters";
        $valid = false;
    }

    if(empty($confirm)){
        $confirmErr = "Please confirm your password";
        $valid = false;
    }

    if($valid){

        if(!$user){
            $oldErr = "User not found";
        }
        elseif(!password_verify($old, $user['password'])){
            $oldErr = "Wrong old password";
        }
        elseif($new !== $confirm){
            $confirmErr = "Passwords do not match";
        }
        else{

            $hash = password_hash($new, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
            $stmt->bind_param("si", $hash, $id);
            $stmt->execute();

            $_SESSION['toast'] = "Password updated successfully!";
            header("Location: profile.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Change Password</title>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">

<style>
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
.error {
    color:red;
    font-weight:bold;
    font size: 10px;
    margin-top:0px;
}

body{
    font-family:'Plus Jakarta Sans',sans-serif;
    background:var(--brand-green);
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    flex-direction:column;
    padding-top:100px;
}

nav{
    position:fixed;
    top:0;
    width:100%;
    padding:18px 8%;
    display:flex;
    justify-content:flex-end;
    background:var(--brand-green);
    border-bottom:1px solid rgba(255,255,255,0.1);
}

.btn-home-nav{
    color:white;
    text-decoration:none;
    border:2px solid var(--brand-orange);
    padding:10px 20px;
    border-radius:50px;
    font-weight:700;
}

.card{
    width:450px;
    background:var(--brand-soft);
    color:#111;
    padding:40px;
    border-radius:30px;
    box-shadow:0 18px 35px rgba(0,0,0,0.25);
    text-align:center;
}

input{
    width:100%;
    padding:12px;
    margin-bottom:12px;
    border-radius:12px;
    border:1px solid #ddd;
    outline:none;
}

.btn{
    width:100%;
    padding:12px;
    border-radius:12px;
    font-weight:700;
    border:none;
    cursor:pointer;
    background:var(--brand-orange);
    color:white;
    transition:.3s;
    display:block;
    text-decoration:none;
    margin-top:10px;
}

.btn:hover{
    background:#e85a28;
}

.error{
    color:red;
    margin-bottom:10px;
    font-size:13px;
}
</style>
</head>

<body>

<nav>
    <a href="profile.php" class="btn-home-nav">← Back</a>
</nav>

<div class="card">

<h2>Change Password</h2><br>


<form method="POST">

    <input type="password" name="old_password" placeholder="Old Password" >
    <div class="error"><?= $oldErr; ?></div>

    <input type="password" name="new_password" placeholder="New Password" >
    <div class="error"><?= $newErr; ?></div>

    <input type="password" name="confirm_password" placeholder="Confirm Password" >
    <div class="error"><?= $confirmErr; ?></div>

    <button class="btn" type="submit">Update Password</button>

</form>

<a href="profile.php" class="btn">Cancel</a>

</div>

</body>
</html>