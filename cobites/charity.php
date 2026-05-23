<?php
require "auth.php";
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$sql = "SELECT * FROM donations 
        WHERE status = 'available' 
        AND remaining_quantity > 0
        ORDER BY created_at DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Charity Dashboard | Cobites</title>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
  * {
  box-sizing: border-box;
}
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

body {
  font-family: 'Plus Jakarta Sans', sans-serif;
  background: var(--bg);
  color: white;
  margin: 0;
  overflow-x: hidden;
}

nav {
    position: fixed;
    top: 0;
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 8%;
    background: #102f15;
    z-index: 1000;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
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
    transition: 0.3s;
}

.btn-home-nav:hover {
    background: #ff6b35;
    color: white;
    transform: translateY(-2px);
}
.hero {
  text-align: center;
  padding: 160px 5% 40px;
  margin-top: 80px; 
}

.hero h1 {
  font-size: 2.5rem;
}

.grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 25px;
  padding: 40px 5%;
  max-width: 1200px;
  margin: 0 auto;
}

.card {
  background: #eaf1b1;
  color: black;
  border-radius: 20px;
  padding: 20px;
  box-shadow: 0 20px 40px rgba(0,0,0,0.3);
  transition: 0.3s;
}

.card:hover {
  transform: translateY(-5px);
}

.tag {
  display: inline-block;
  padding: 5px 10px;
  background: var(--brand);
  color: #fff5f2;
  border-radius: 8px;
  margin-bottom: 10px;
  font-size: 0.8rem;
}

.info {
  font-size: 0.9rem;
  color: #475569;
  margin-bottom: 10px;
}

.btn {
  width: 100%;
  padding: 12px;
  background: var(--brand);
  border: none;
  color: white;
  border-radius: 10px;
  font-weight: bold;
  cursor: pointer;
}

.btn:hover {
  background: #e65a2b;
}
.footer {
    background: #081a0c;
    padding: 60px 8% 30px;
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 50px;
    color: white;
    margin-top: 60px;
}

.footer-logo {
    font-family: 'Playfair Display', serif;
    font-size: 1.6rem;
    font-weight: 800;
    color: white;
    text-decoration: none;
}

.footer-logo span {
    color: #ff6b35;
}

.footer-col h4 {
    color: #ff6b35;
    margin-bottom: 15px;
}

.footer-col ul {
    list-style: none;
    padding: 0;
}

.footer-col ul li {
    margin-bottom: 10px;
}

.footer-col ul li a {
    color: white;
    text-decoration: none;
    opacity: 0.7;
}

.footer-col ul li a:hover {
    opacity: 1;
    color: #ff6b35;
}

.footer-bottom {
    grid-column: 1 / -1;
    text-align: center;
    margin-top: 20px;
    font-size: 0.85rem;
    opacity: 0.6;
    border-top: 1px solid rgba(255,255,255,0.1);
    padding-top: 20px;
}

@media(max-width: 900px){
    .footer {
        grid-template-columns: 1fr;
        text-align: center;
    }
}

@media(max-width: 900px){
  .grid { grid-template-columns: repeat(2, 1fr); }
}

@media(max-width: 600px){
  .grid { grid-template-columns: 1fr; }
}
</style>
</head>

<body>

<nav>
    <a href="homepage.php" class="logo">Cobites<span>.</span></a>

    <div class="nav-actions" style="display: flex; gap: 15px;">
        <a href="charity_history.php" class="btn-home-nav" style="border-color: var(--brand-sage);">
            <i class="fas fa-history"></i> My History
        </a>
        <a href="homepage.php" class="btn-home-nav">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>
    </div>
</nav>

<header class="hero">
  <h1>Available Donations for <span style="color:#ff6b35;">Charities</span></h1>
  <p>Accept food and distribute it to those in need</p>
</header>

<section class="grid">

<?php if ($result && $result->num_rows > 0): ?>

    <?php while($row = $result->fetch_assoc()): ?>

        <div class="card">

            <span class="tag">
                <i class="fas fa-box"></i> <?= htmlspecialchars($row['category']) ?>
            </span>

            <h3><?= htmlspecialchars($row['food_item_name']) ?></h3>

            <p class="info">
                <strong>Available:</strong> <?= $row['remaining_quantity'] ?> portions
            </p>

            <p class="info">
                <i class="fas fa-map-marker-alt"></i>
                <?= htmlspecialchars($row['pickup_address']) ?>
            </p>

            <p class="info">
                <i class="fas fa-clock"></i>
                Expires: <?= $row['best_before'] ?>
            </p>

            <p class="info">
                <i class="fas fa-info-circle"></i>
                <?= $row['food_condition'] ?>
            </p>

            <form method="GET" action="accept_donation.php" style="margin:0;">
             <input type="hidden" name="id" value="<?= $row['id'] ?>">
               <button type="submit" class="btn">
                  Accept Donation
               </button>
            </form>

        </div>

    <?php endwhile; ?>

<?php else: ?>

    <p style="text-align:center; grid-column:1/-1;">
        No donations available right now.
    </p>

<?php endif; ?>

</section>
<footer class="footer">

    <div class="footer-col">
        <a href="#" class="logo">Cobites<span>.</span></a>

        <p style="color: var(--slate); margin-top: 20px; font-size: 0.9rem; line-height: 1.6;">
            Cobites is a smart food-logistics platform connecting restaurants,
            donors, volunteers, and NGOs to reduce food waste and fight hunger
            through real-time verified delivery systems.
        </p>

        <div style="margin-top:20px; font-size:1.2rem;">
            <i class="fab fa-facebook" style="margin-right:15px; cursor:pointer;"></i>
            <i class="fab fa-instagram" style="margin-right:15px; cursor:pointer;"></i>
            <i class="fab fa-linkedin" style="margin-right:15px; cursor:pointer;"></i>
            <i class="fab fa-twitter" style="cursor:pointer;"></i>
        </div>
    </div>


    <div class="footer-col">
        <h4>Platform</h4>
        <ul>
            <li><a href="#stats"> Live Analytics</a></li>
            <li><a href="#process">How it Works</a></li>
<li><a href="#reviews-section">Latest news</a></li>            <li><a href="index.php">Join Cobites</a></li>
        </ul>
    </div>


 <div class="footer-col">
    <h4>Support</h4>
    <ul>
        <li><a href="Help Center.php">Help Center</a></li>
<li><a href="Privacy_Policy.php">Privacy Policy</a></li>
<li><a href="Terms and Conditions.php">Terms & Conditions</a></li>
        <li><a href="homepage.php#contact">Contact Us</a></li>
    </ul>
</div>

    <div style="
        grid-column: 1 / -1;
        border-top: 1px solid rgba(255,255,255,0.05);
        padding-top: 25px;
        text-align:center;
        font-size:0.8rem;
        opacity:0.6;
    ">
        © 2026 Cobites Logistics Network — Fighting Food Waste with Technology 🌍
    </div>

</footer>

</body>
</html>