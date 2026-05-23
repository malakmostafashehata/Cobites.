<?php
require "db.php";
require "auth.php"; // This file starts the session and handles 'Remember Me'

// 1. Double check security: If no session, kick to login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Handle Actions (Accept or Deliver)
if (isset($_GET['id']) && isset($_GET['action'])) {

    $order_id = intval($_GET['id']); // Using intval from your notes for security
    $action = $_GET['action'];

    if ($action == 'accept') {
        // Assign the order to THIS user and change status
        $sql = "UPDATE orders 
                SET status = 'accepted', 
                    delivery_user_id = ? 
                WHERE id = ? AND status = 'pending'";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $order_id);
        $stmt->execute();
        
        header("Location: delivery.php");
        exit();
    }

    if ($action == 'deliver') {
        // Finalize the delivery
        $sql = "UPDATE orders 
                SET status = 'delivered' 
                WHERE id = ? 
                AND delivery_user_id = ? 
                AND status = 'accepted'";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $order_id, $user_id);
        $stmt->execute();
        
        header("Location: delivery.php");
        exit();
    }
}

// 3. THE KEY CHANGE: Filtered Fetch Logic
// We show 'pending' orders to everyone.
// We show 'accepted' orders ONLY to the user who accepted them.
$query = "SELECT * FROM orders 
          WHERE status = 'pending' 
          OR (status = 'accepted' AND delivery_user_id = ?) 
          ORDER BY created_at DESC";

$stmt_list = $conn->prepare($query);
$stmt_list->bind_param("i", $user_id);
$stmt_list->execute();
$result = $stmt_list->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Logistics | Cobites</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --brand-orange: #ff6b35; 
            --brand-green: #102f15; 
            --brand-soft: #eaf1b1; 
            --white: #ffffff;
            --slate: #94a3b8; 
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--brand-green); min-height: 100vh; color: #102f15; line-height: 1.6; }
        
        /* z-index from your notes: above all */
        nav { position: fixed; top: 0; width: 100%; display: flex; justify-content: space-between; align-items: center; padding: 18px 8%; background: var(--brand-green); z-index: 1000; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        
        .logo { font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 800; color: white; text-decoration: none; }
        .logo span { color: var(--brand-orange); }
        .btn-nav { display: flex; align-items: center; gap: 10px; color: white; text-decoration: none; font-weight: 700; font-size: 0.95rem; transition: 0.3s; border: 2px solid var(--brand-orange); padding: 10px 22px; border-radius: 50px; }
        .btn-nav:hover { background: var(--brand-orange); transform: translateY(-2px); }
        
        .container { max-width: 1100px; margin: 0 auto; padding: 140px 5% 100px; }
        h1 { font-family: 'Playfair Display', serif; font-size: clamp(2rem, 5vw, 3rem); margin-bottom: 40px; text-align: center; color: white; }
        h1 span { color: var(--brand-orange); }
        
        .order-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px; }
        
        /* flex-direction column from your notes */
        .order-card { background: var(--brand-soft); border-radius: 30px; padding: 30px; display: flex; flex-direction: column; justify-content: space-between; transition: 0.3s; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .order-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.25); }

        .food-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
        .qty-badge { background: var(--brand-orange); color: white; padding: 5px 15px; border-radius: 50px; font-weight: 800; font-size: 0.8rem; }
        
        .route-section { margin: 20px 0; }
        .route-step { display: flex; gap: 15px; margin-bottom: 15px; }
        .route-step i { color: var(--brand-orange); font-size: 1.1rem; margin-top: 4px; }
        .route-step small { color: #6b7280; text-transform: uppercase; font-size: 0.7rem; font-weight: 800; display: block; }
        .route-step strong { display: block; font-size: 1rem; margin-top: 2px; color: var(--brand-green); }
        
        .btn-action { width: 100%; padding: 15px; border-radius: 15px; border: none; font-weight: 800; cursor: pointer; text-decoration: none; text-align: center; display: block; transition: 0.3s; }
        .btn-accept { background: var(--brand-orange); color: white; }
        .btn-deliver { background: var(--brand-green); color: white; }
        
        footer { background: #081a0c; padding: 80px 8% 40px; border-top: 1px solid rgba(255, 255, 255, 0.05); display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 60px; color: white; }
        
        /* Media query from your notes for mobile */
        @media (max-width: 768px) {
            footer { grid-template-columns: 1fr; gap: 30px; text-align: center; }
            .container { padding-top: 100px; }
        }
    </style>
</head>
<body>

<nav>
    <a href="homepage.php" class="logo">Cobites<span>.</span></a>
    <div style="display: flex; gap: 15px;">
        <a href="history_delivery.php" class="btn-nav" style="border-color: #728c5a;"><i class="fas fa-history"></i> My History</a>
        <a href="homepage.php" class="btn-nav"><i class="fas fa-arrow-left"></i> Back to Home</a>
    </div>
</nav>

<div class="container">
    <h1>Available <span>Deliveries</span></h1>
    <div class="order-grid">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="order-card">
                <div>
                    <div class="food-header">
                        <h3 style="font-family: 'Playfair Display'; color: var(--brand-green);"><?php echo htmlspecialchars($row['food_item_name']); ?></h3>
                        <span class="qty-badge"><?php echo $row['quantity']; ?> Portions</span>
                    </div>
                    <div class="route-section">
                        <div class="route-step">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <small>Pickup (Donor)</small>
                                <strong><?php echo htmlspecialchars($row['donor_address']); ?></strong>
                                <p style="font-size: 0.8rem; opacity: 0.8;">Contact: <?php echo $row['donor_phone']; ?></p>
                            </div>
                        </div>
                        <div class="route-step">
                            <i class="fas fa-hand-holding-heart"></i>
                            <div>
                                <small>Deliver To (Charity)</small>
                                <strong><?php echo htmlspecialchars($row['charity_name']); ?></strong>
                                <p style="font-size: 0.8rem; opacity: 0.8;">Contact: <?php echo $row['charity_phone']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($row['status'] == 'pending'): ?>
                    <form class="acceptForm" method="GET">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <input type="hidden" name="action" value="accept">
                        <button type="submit" class="btn-action btn-accept">Accept Pickup</button>
                    </form>
                <?php else: ?>
                    <form class="deliverForm" method="GET">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <input type="hidden" name="action" value="deliver">
                        <button type="submit" class="btn-action btn-deliver">Confirm Delivered</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</div>

 <footer>

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


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll(".acceptForm").forEach(form => {
    form.addEventListener("submit", function(e){
        e.preventDefault();
        let f = this;
        Swal.fire({
            title: "Accept this order?",
            text: "You will be responsible for this delivery.",
            icon: "warning",
            background: "#0b2310",
            color: "#EAF1B1",
            showCancelButton: true,
            confirmButtonText: "Yes, accept",
            confirmButtonColor: "#ff6b35",
        }).then((result) => {
            if (result.isConfirmed) {
                f.submit();
            }
        });
    });
});
</script>

</body>
</html>