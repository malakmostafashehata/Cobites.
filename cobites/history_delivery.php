<?php
include "db.php";
require "auth.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$current_user = $_SESSION['full_name'] ?? 'Volunteer';

$query = "SELECT * FROM orders 
          WHERE status = 'delivered'
          AND delivery_user_id = ?
          ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delivery History | Cobites</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --brand-orange: #ff6b35; 
            --brand-green: #102f15; 
            --white: #ffffff;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--brand-green); 
            color: white; 
            padding: 60px 40px; 
            margin: 0;
        }

        .container { max-width: 1000px; margin: 0 auto; }

        .header-box { margin-bottom: 40px; }

        h1 { 
            font-family: 'Playfair Display', serif; 
            font-size: 2.5rem; 
            margin-top: 20px; 
        }

        h1 span { color: var(--brand-orange); }

        table { 
            width: 100%; 
            background: white; 
            color: #102f15; 
            border-radius: 20px; 
            overflow: hidden; 
            border-collapse: collapse; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        th { 
            background: var(--brand-orange); 
            color: white; 
            padding: 20px; 
            text-align: left; 
            font-size: 0.8rem; 
            text-transform: uppercase; 
        }

        td { 
            padding: 20px; 
            border-bottom: 1px solid #eee; 
            font-size: 0.95rem; 
        }

        .status-badge { 
            background: #dcfce7; 
            color: #166534; 
            padding: 6px 12px; 
            border-radius: 50px; 
            font-weight: 800; 
            font-size: 0.75rem; 
            display: inline-flex; 
            align-items: center; 
            gap: 5px; 
        }

        .back-link { 
            color: var(--brand-orange); 
            text-decoration: none; 
            font-weight: 700; 
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
            margin-bottom: 20px; 
            transition: 0.3s; 
        }

        .back-link:hover { transform: translateX(-5px); }
    </style>
</head>

<body>

<div class="container">

    <div class="header-box">
        <a href="delivery.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Active Pickups
        </a>

        <h1>Completed <span>Deliveries</span></h1>

        <p style="opacity: 0.7;">
            Keep it up, <?= htmlspecialchars($current_user); ?>!
            You've completed <?= $result ? $result->num_rows : 0; ?> deliveries.
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Food Item</th>
                <th>Charity</th>
                <th>From</th>
                <th>Completed On</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($row['food_item_name']); ?></strong><br>
                            <small style="opacity: 0.6;">
                                <?= $row['quantity']; ?> Portions
                            </small>
                        </td>

                        <td><?= htmlspecialchars($row['charity_name']); ?></td>

                        <td><?= htmlspecialchars($row['donor_address']); ?></td>

                        <td>
                            <?= date('M d, Y', strtotime($row['created_at'])); ?>
                        </td>

                        <td>
                            <span class="status-badge">
                                <i class="fas fa-check-circle"></i> Delivered
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding:50px; opacity:0.5;">
                        No delivery history found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>

    </table>

</div>

</body>
</html>