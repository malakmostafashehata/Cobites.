<?php
include "db.php";
require "auth.php";
if (!isset($_SESSION['full_name'])) {
    header("Location: index.php");
    exit();
}

$current_user = $_SESSION['full_name'];
$stmt = $conn->prepare("SELECT * FROM donations WHERE donor_name = ? ORDER BY created_at DESC");
$stmt->bind_param("s", $current_user);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Donations | Cobites</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #102f15; color: white; padding: 40px; }
        .container { max-width: 900px; margin: 0 auto; }
        table { width: 100%; background: white; color: black; border-radius: 15px; overflow: hidden; border-collapse: collapse; margin-top: 20px; }
        th { background: #ff6b35; color: white; padding: 15px; text-align: left; }
        td { padding: 15px; border-bottom: 1px solid #eee; }
        .qty-badge { background: #eaf1b1; padding: 5px 10px; border-radius: 8px; font-weight: 800; font-size: 0.85rem; border: 1px solid #102f15; }
    </style>
</head>
<body>
    <div class="container">
        <a href="volunteer.php" style="color: #ff6b35; text-decoration: none;">← Back to Donate</a>
        <h1>Your Donation History, <span><?= htmlspecialchars($current_user); ?></span></h1>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Original Qty</th>
                    <th>Remaining</th>
                    <th>Category</th>
                    <th>Date Posted</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['food_item_name']); ?></td>
                        <td><?= htmlspecialchars($row['quantity']); ?></td>
                        <td><span class="qty-badge"><?= htmlspecialchars($row['remaining_quantity']); ?> left</span></td>
                        <td><?= ucfirst(htmlspecialchars($row['category'])); ?></td>
                        <td><?= date('M d, Y', strtotime($row['created_at'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>