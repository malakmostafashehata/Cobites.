<?php
require "auth.php";
include "db.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$errors = $_SESSION['form_errors'] ?? [];
$old_data = $_SESSION['form_data'] ?? [];
$success = $_GET['success'] ?? false;

unset($_SESSION['form_errors'], $_SESSION['form_data']);

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donate Food | Cobites</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --brand-orange: #ff6b35; 
            --brand-green: #102f15; 
            --brand-sage: #728c5a;
            --brand-soft: #eaf1b1; 
            --white: #ffffff;
            --text-dark: #000000;
            --slate: #94a3b8; 
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--brand-green); 
            min-height: 100vh;
            color: white;
            line-height: 1.6;
        }

        nav {
            position: fixed; top: 0; width: 100%;
            display: flex; justify-content: space-between; align-items: center;
            padding: 18px 8%; 
            background: var(--brand-green); 
            z-index: 1000;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1); 
        }

        .logo { font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 800; color: white; text-decoration: none; }
        .logo span { color: var(--brand-orange); }
        
        .btn-home-nav { 
            display: flex;
            align-items: center;
            gap: 10px;
            color: white; 
            text-decoration: none; 
            font-weight: 700; 
            font-size: 0.95rem; 
            transition: 0.3s;
            border: 2px solid var(--brand-orange);
            padding: 10px 22px;
            border-radius: 50px;
        }
        .btn-home-nav:hover { 
            background: var(--brand-orange);
            color: white;
            transform: translateY(-2px);
        }

        .hero {
            text-align: center;
            padding: 160px 5% 40px;
        }

        .hero h1 {
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            font-family: 'Playfair Display', serif;
            margin-bottom: 15px;
            color: white;
        }

        .hero span { color: var(--brand-orange); }

        .donation-container {
            max-width: 800px;
            margin: 0 auto 100px;
            background: var(--brand-soft); 
            border-radius: 35px;
            overflow: hidden;
            box-shadow: 0 40px 100px rgba(0,0,0,0.5);
            color: var(--text-dark);
        }

        .form-progress {
            background: rgba(16, 47, 21, 0.05);
            padding: 25px 40px;
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid rgba(16, 47, 21, 0.1);
            font-size: 0.9rem;
            font-weight: 600;
        }

        .form-section { padding: 40px; }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }

        .category-item {
            border: 2px solid var(--brand-green);
            background: var(--white);
            padding: 20px;
            border-radius: 20px;
            text-align: center;
            cursor: pointer;
            transition: 0.3s ease;
            color: var(--brand-green);
        }

       .category-item input {
    position: absolute;
    opacity: 0;
    width: 1px;
    height: 1px;
}

        .category-item:hover { transform: translateY(-5px); border-color: var(--brand-orange); }

        .category-item:has(input:checked) {
            background: var(--brand-orange);
            color: white;
            border-color: var(--brand-orange);
        }

        .category-item i {
            display: block;
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .input-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        label {
            font-weight: 800;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: block;
            margin-bottom: 8px;
            color: var(--brand-green);
        }

        input, select, textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid rgba(16, 47, 21, 0.1);
            background: white;
            border-radius: 12px;
            outline: none;
            font-family: inherit;
        }

        input:focus { border-color: var(--brand-orange); }

        .btn-submit {
            background: var(--brand-orange);
            color: white;
            border: none;
            width: 100%;
            padding: 20px;
            border-radius: 18px;
            font-size: 1.1rem;
            font-weight: 800;
            cursor: pointer;
            transition: 0.4s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            box-shadow: 0 10px 20px rgba(255, 107, 53, 0.2);
        }

        .btn-submit:hover { background: #e65a2b; transform: translateY(-3px); box-shadow: 0 15px 30px rgba(255, 107, 53, 0.4); }

        footer {
            background: #081a0c;
            padding: 80px 8% 40px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            display: grid;
            grid-template-columns: 2fr 1fr 1fr; 
            gap: 60px;
            color: white;
            text-align: left;
        }

        @media (max-width: 900px) {
            footer { grid-template-columns: 1fr; gap: 40px; text-align: center; }
        }
        .footer-col { display: flex; flex-direction: column; }
        .footer-col h4 { color: var(--brand-orange); margin-bottom: 20px; font-size: 1rem; }
        .footer-col ul { list-style: none; padding: 0; }
        .footer-col ul li { margin-bottom: 12px; opacity: 0.7; font-size: 0.9rem; }
        .footer-col ul li a { text-decoration: none; color: var(--white); transition: 0.3s; }
        .footer-col ul li a:hover { color: var(--brand-orange); opacity: 1; }
        .footer-logo { font-family: 'Playfair Display', serif; font-size: 1.6rem; font-weight: 800; color: var(--white); text-decoration: none; }
        .footer-logo span { color: var(--brand-orange); }
    </style>
</head>
<body>

<nav>
    <a href="index.html" class="logo">Cobites<span>.</span></a>
    <div class="nav-actions" style="display: flex; gap: 15px;">
        <a href="history.php" class="btn-home-nav" style="border-color: var(--brand-sage);">
            <i class="fas fa-history"></i> My History
        </a>
        <a href="homepage.php" class="btn-home-nav">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>
    </div>
</nav>

<header class="hero">
    <h1>Share Your <span>Extra</span> Food</h1>
    <p style="color: var(--brand-soft); opacity: 0.8; font-size: 1.1rem;">Your surplus can become a community's solution.</p>
</header>

<main class="donation-container">
    <div class="form-progress">
        <span style="color: var(--brand-orange);">Item Details</span>
        <?php if($success): ?>
            <span style="color: var(--brand-sage); font-weight: 800;">✓ Submission Successful!</span>
        <?php endif; ?>
    </div>

    <form id="donationForm" action="submit_donation.php" method="POST" class="form-section">
        <label>1. Category</label>
<div class="category-grid">
    <label class="category-item">
        <input type="radio" name="category" value="savory" required>
        <i class="fas fa-hamburger"></i>
        <span>Savory</span>
    </label>
    <label class="category-item">
        <input type="radio" name="category" value="sweet">
        <i class="fas fa-cookie-bite"></i>
        <span>Sweet</span>
    </label>
    <label class="category-item">
        <input type="radio" name="category" value="grocery">
        <i class="fas fa-apple-alt"></i>
        <span>Grocery</span>
		<span id="categoryError"
      style="
color:#ff4d4d;
font-size:0.8rem;
font-weight:bold;
display:none;
text-align:center;
margin-top:15px;
width:100%;
"
    Please fill out this field.
</span>
    </label>
</div>

<?php if (!empty($CateErr)): ?>
    <span style="color: #ff4d4d; font-size: 0.8rem; font-weight: bold; display: block; margin-top: -20px; margin-bottom: 20px;">
        <?php echo $CateErr; ?>
    </span>
<?php endif; ?>
        <div class="input-row">
            <div>
                <label>Food Item Name</label>
                <input type="text" name="food_item_name" placeholder="e.g. Fresh Garden Salad" value="<?php echo htmlspecialchars($old_data['food_item_name'] ?? ''); ?>" required>
            </div>
            <div>
                <label>Quantity / Portions</label>
                <input type="number" name="quantity" min="1" placeholder="e.g. 5" value="<?php echo htmlspecialchars($old_data['quantity'] ?? ''); ?>" required>
            </div>
        </div>

        <div class="input-row">
            <div>
                <label>Best Before</label>
                <input type="date" name="best_before" 
                       min="<?php echo date('Y-m-d'); ?>" 
                       max="<?php echo date('Y-m-d', strtotime('+7 days')); ?>" 
                       value="<?php echo htmlspecialchars($old_data['best_before'] ?? ''); ?>" required>
            </div>
            <div>
                <label>Condition</label>
                <select name="food_condition">
                    <option value="fresh" <?php echo ($old_data['food_condition'] ?? '') === 'fresh' ? 'selected' : ''; ?>>Freshly Cooked</option>
                    <option value="good" <?php echo ($old_data['food_condition'] ?? '') === 'good' ? 'selected' : ''; ?>>Sealed Packaging</option>
                    <option value="needs_fast_delivery" <?php echo ($old_data['food_condition'] ?? '') === 'needs_fast_delivery' ? 'selected' : ''; ?>>Raw / Unprocessed</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom: 30px;">
            <label>2. Pickup Address</label>
            <input type="text" name="pickup_address" placeholder="Street, Building, Unit Number" value="<?php echo htmlspecialchars($old_data['pickup_address'] ?? ''); ?>" required>
        </div>

        <button type="submit" class="btn-submit">
            Submit 
            <i class="fas fa-paper-plane"></i>
        </button>
    </form>
</main>

<footer>
    <div class="footer-col">
        <a href="#" class="logo">Cobites<span>.</span></a>
        <p style="color: var(--slate); margin-top: 20px; font-size: 0.9rem; line-height: 1.6;">
            Cobites is a smart food-logistics platform connecting restaurants, donors, volunteers, and NGOs.
        </p>
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
</footer>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('donationForm').addEventListener('submit', function(e) {

    const categorySelected = document.querySelector('input[name="category"]:checked');

    if (!categorySelected) {
        e.preventDefault();

        Swal.fire({
            icon: 'error',
            title: 'Missing Category',
            text: 'Please fill out this field.',
            confirmButtonColor: '#ff6b35',
            background: '#eaf1b1',
            color: '#102f15'
        });

        return;
    }

    if (!this.checkValidity()) {
        return;
    }

    e.preventDefault();

    Swal.fire({
        title: "Are you sure?",
        text: "When you donate, you cannot delete or edit this post later. Do you want to proceed?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ff6b35",
        cancelButtonColor: "#102f15",
        confirmButtonText: "Yes, I'm sure!",
        cancelButtonText: "No, cancel",
        background: "#eaf1b1",
        color: "#102f15"
    }).then((result) => {
        if (result.isConfirmed) {
            this.submit();
        }
    });
});
</script>
</body>
</html>