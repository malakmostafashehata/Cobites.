<?php
include "db.php";
require "auth.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("Donation not found.");
}

$donation_id = (int)$_GET['id'];

$sql = "SELECT * FROM donations 
        WHERE id = ? 
        AND status = 'available'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $donation_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Donation unavailable.");
}

$donation = $result->fetch_assoc();

$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Accept Donation | Cobites</title>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>

:root {
  --brand: #ff6b35;
  --bg: #102f15;
  --brand-green: #102f15; 
  --brand-sage: #728c5a;
  --brand-soft: #eaf1b1; 
  --white: #ffffff;
  --text-dark: #000000;
  --slate: #94a3b8; 
}

body{
    font-family:'Plus Jakarta Sans',sans-serif;
    background:#102f15;
    margin:0;
    color:white;
}
nav {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    box-sizing: border-box;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 8%;
    background: #102f15;
    z-index: 1000;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    overflow: visible;
}

.logo {
    font-family: 'Playfair Display', serif;
    font-size: 1.8rem;
    font-weight: 800;
    color: white;
    text-decoration: none;
}

.logo span {
    color: #ff6b35;
}

.btn-home-nav {
    display: flex;
    align-items: center;
    gap: 10px;
    color: white;
    text-decoration: none;
    font-weight: 700;
    font-size: 0.95rem;
    border: 2px solid #ff6b35;
    padding: 10px 22px;
    border-radius: 50px;
    white-space: nowrap;
}

.container{
    max-width:900px;
    margin:140px auto;
    background: #eaf1b1;
    color: #102f15;
    padding:35px;
    border-radius:30px;
}

.header-title h1{
    margin:10px 0 25px;
    font-size:2rem;
}

.info-grid{
    display:grid;
    grid-template-columns:repeat(2, 1fr);
    gap:18px;
    margin-bottom:30px;
}

.info-box{
    background:white;
    padding:16px;
    border-radius:14px;
    border:2px solid #102f15;
}

.info-box strong{
    display:block;
    margin-bottom:6px;
    color: #102f15;
    font-size:0.8rem;
}

.info-box p{
    margin:0;
    font-size:0.9rem;
}

label{
    display:block;
    margin-top:15px;
    margin-bottom:6px;
    font-weight:700;
    font-size:0.85rem;
}

input{
    width:100%;
    padding:12px;
    border-radius:10px;
    border:2px solid transparent; 
    font-size:0.9rem;
    outline:none;
    background:white;
    transition:0.2s ease;
}

input:hover,
input:focus{
    border-color:#ff6b35; 
}

button{
    width:100%;
    margin-top:25px;
    padding:14px;
    border:none;
    border-radius:14px;
    background:#ff6b35;
    color:white;
    font-size:0.95rem;
    font-weight:700;
    cursor:pointer;
}

button:hover{
    background:#e65a2b;
}

.swal2-popup {
    border-radius: 16px !important;
}


</style>
</head>

<body>

<nav>
    <a href="charity.php" class="logo">Cobites<span>.</span></a>

    <div class="nav-actions" style="display: flex; gap: 15px;">
        <a href="charity_history.php" class="btn-home-nav" style="border-color: var(--brand-sage);">
            <i class="fas fa-history"></i> My History
        </a>
        <a href="charity.php" class="btn-home-nav">
            <i class="fas fa-arrow-left"></i> Back to Charity
        </a>
    </div>
</nav>

<div class="container">

<div class="header-title">
    <h1><?= htmlspecialchars($donation['food_item_name']) ?></h1>
</div>

<div class="info-grid">

    <div class="info-box">
        <strong>Category</strong>
        <p><?= htmlspecialchars($donation['category']) ?></p>
    </div>

    <div class="info-box">
        <strong>Available</strong>
        <p><?= $donation['remaining_quantity'] ?></p>
    </div>

    <div class="info-box">
        <strong>Pickup Address</strong>
        <p><?= htmlspecialchars($donation['pickup_address']) ?></p>
    </div>

    <div class="info-box">
        <strong>Best Before</strong>
        <p><?= $donation['best_before'] ?></p>
    </div>

</div>

<form id="acceptForm" action="process_accept.php" method="POST">

    <input type="hidden" name="donation_id" value="<?= $donation['id'] ?>">

    <label>Quantity Needed</label>
    <input type="number"
       name="quantity"
       inputmode="numeric"
       min="1"
       max="<?= $donation['remaining_quantity'] ?>"
       required>

    <label>Delivery Date</label>
    <input type="date" name="delivery_date" min="<?= $today ?>" max="<?= $donation['best_before'] ?>">

    <button type="submit">Confirm Order</button>

</form>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.getElementById("acceptForm").addEventListener("submit", function(e){
    e.preventDefault();

    let form = this;

    Swal.fire({
        title: "Confirm Order?",
        text: "After you confirm, you cannot edit your order.",
        icon: "warning",
        background: "#0b2310",
        color: "#EAF1B1",
        showCancelButton: true,
        confirmButtonText: "Yes, confirm",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#ff6b35",
        cancelButtonColor: "#1f3a24",
        allowOutsideClick: false,

        preConfirm: () => {

            Swal.showLoading();

            return new Promise((resolve) => {

                setTimeout(() => {
                    resolve(true);
                }, 1200); 
            });
        }

    }).then((result) => {

        if (result.isConfirmed) {
            Swal.fire({
                title: "Confirmed!",
                text: "Processing your order...",
                icon: "success",
                background: "#0b2310",
                color: "#EAF1B1",
                confirmButtonColor: "#ff6b35",
                timer: 1200,
                showConfirmButton: false,
                willClose: () => {
                    form.submit(); 
                }
            });

        }
    });

});
</script>
</body>
</html>