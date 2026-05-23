<?php
require "auth.php";
require "db.php";
if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$full_nameErr = "";
$phoneErr = "";
$addressErr = "";

$result = mysqli_query($conn,"SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($result);

function test_input($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $full_name = test_input($_POST['full_name']);
    $phone     = test_input($_POST['phone']);
    $address   = test_input($_POST['address']);
    $password  = test_input($_POST['password']);

    $valid = true;

    if(empty($full_name)){
        $full_nameErr = '* Name is Required';
        $valid = false;
    }
    elseif(!preg_match('/^[a-zA-Z0-9 ]*$/',$full_name)){
        $full_nameErr='* Only Characters, Numbers and Spaces Allowed';
        $valid = false;
    }

    if(empty($phone)){
        $phoneErr = '* Phone number is required';
        $valid = false;
    }
    elseif(strlen($phone) != 11){
        $phoneErr = '* Phone must be 11 numbers';
        $valid = false;
    }
    elseif(!preg_match('/^[0-9]*$/',$phone)){
        $phoneErr = '* Invalid phone';
        $valid = false;
    }

    if(empty($address)){
        $addressErr = '* Address is required';
        $valid = false;
    }

    if($valid){

        if(!empty($password)){

            $hashed = password_hash($password, PASSWORD_DEFAULT);

            mysqli_query($conn,"
                UPDATE users SET
                full_name='$full_name',
                phone='$phone',
                address='$address',
                password='$hashed'
                WHERE id='$user_id'
            ");

        }else{

            mysqli_query($conn,"
                UPDATE users SET
                full_name='$full_name',
                phone='$phone',
                address='$address'
                WHERE id='$user_id'
            ");
        }

        $_SESSION['full_name'] = $full_name;
        $_SESSION['toast'] = "Profile updated successfully!";

        header("Location: profile.php");
        exit();
    }

    $user['full_name'] = $full_name;
    $user['phone'] = $phone;
    $user['address'] = $address;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Profile | Cobites</title>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">

<style>

:root{
    --brand-orange:#ff6b35;
    --brand-green:#102f15;
    --brand-soft:#eaf1b1;
}

*{margin:0;padding:0;box-sizing:border-box;}

body{
    font-family:'Plus Jakarta Sans',sans-serif;
    background:var(--brand-green);
    color:white;
}

nav{
    position:fixed;
    top:0;
    width:100%;
    display:flex;
    justify-content:space-between;
    padding:18px 8%;
    background:var(--brand-green);
    border-bottom:1px solid rgba(255,255,255,0.1);
}

.btn-back{
    text-decoration:none;
    color:white;
    border:2px solid var(--brand-orange);
    padding:10px 18px;
    border-radius:50px;
}

.hero{
    text-align:center;
    padding:140px 5% 30px;
}

.hero h1{
    font-family:'Playfair Display';
    font-size:2.8rem;
}

.hero span{color:var(--brand-orange);}
.btn-row{
    display:flex;
    gap:10px;
    margin-top:15px;
}

.btn-form{
    flex:1;
}

.btn-form button{
    width:100%;
}
.error {
    color:red;
    font-weight:bold;
    font size: 10px;
    margin-top:0px;
}

.btn{
    padding:10px;
    font-size:13px;
    border-radius:10px;
}
.card{
    max-width:450px;
    margin:0 auto 80px;
    background:var(--brand-soft);
    color:#111;
    padding:40px;
    border-radius:30px;
}

input{
    width:100%;
    padding:12px;
    margin-bottom:8px;
    margin-top:8px;
    border-radius:10px;
    border:1px solid #ddd;
}.btn-row{
    display:flex;
    gap:10px;
    margin-top:15px;
}

.btn-row form{
    flex:1;
}

.btn{
    padding:10px 12px;   
    font-size:13px;      
}

.btn-row button{
    width:100%;
}
.btn{
    display:block;
    text-align:center;
    padding:14px;
    font-weight:700;
    text-decoration:none;
    margin-top:10px;
    transition:.3s;
}
.btn-row{
    display:flex;
    gap:12px;
    margin-top:10px;
}

.btn-row form{
    flex:1;
}

.btn-row button{
    width:100%;
}
.btn-save{
    background:#ff6b35;
    color:white;
}

.btn-save:hover{
    background:#e85a28;
}

.btn-delete{
    background:#ff6b35;
    color:white;
}

.btn-delete:hover{
    background:#e85a28;
}

.btn-cancel{
    background:white;
    color:#111;
    border:1px solid #ddd;
}
.btn-cancel:hover{
    background:#f2f2f2;
}.btn{
    display:block;
    width:100%;
    margin-top:12px;
}

</style>
</head>

<body>

<nav>
    <div></div>
    <a href="homepage.php" class="btn-back">← Back Home</a>
</nav>

<header class="hero">
    <h1>Edit <span>Profile</span></h1>
</header>
<div class="card">

    <form action="" method="POST">


        <input type="text" name="full_name"
        value="<?= htmlspecialchars($user['full_name']); ?>" >
        <h5 class='error'> <?php echo $full_nameErr; ?> </h5>

        <input type="text" name="phone"
        value="<?= htmlspecialchars($user['phone']); ?>">
        <h5 class='error'> <?php echo $phoneErr; ?> </h5>

        <input type="text" name="address"
        value="<?= htmlspecialchars($user['address']); ?>">
        <h5 class='error'> <?php echo $addressErr; ?> </h5>

        <div class="btn-row">

            <button class="btn btn-save" type="submit">
                Save
            </button>

    </form>

            <form action="delete_account.php" method="POST"
            onsubmit="return confirm('Are you sure? This cannot be undone!');">

                <button type="submit" class="btn btn-delete">
                    Delete
                </button>

            </form>

        </div>

        <a href="profile.php" class="btn btn-cancel">
            Cancel
        </a>

</div>

</body>


</html>