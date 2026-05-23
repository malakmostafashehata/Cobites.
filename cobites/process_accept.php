<?php
include "db.php";
require "auth.php"; 
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit();
}

try {

    $user_id = $_SESSION['user_id'];

    $user_stmt = $conn->prepare("
        SELECT full_name, phone, address 
        FROM users 
        WHERE id = ?
    ");
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user = $user_stmt->get_result()->fetch_assoc();

    if (!$user) {
        die("User not found.");
    }

    $charity_name    = $user['full_name'];
    $charity_phone   = $user['phone'];
    $charity_address = $user['address'];

    $donation_id   = (int)($_POST['donation_id'] ?? 0);
    $quantity      = (int)($_POST['quantity'] ?? 0);
    $delivery_date = $_POST['delivery_date'] ?? null;

    if ($donation_id <= 0 || $quantity <= 0) {
        die("Invalid input.");
    }

    $donation_stmt = $conn->prepare("
        SELECT * FROM donations WHERE id = ?
    ");
    $donation_stmt->bind_param("i", $donation_id);
    $donation_stmt->execute();
    $donation = $donation_stmt->get_result()->fetch_assoc();

    if (!$donation) {
        die("Donation not found.");
    }

    if ($quantity > $donation['remaining_quantity']) {
        die("Quantity exceeds stock.");
    }

    if ($delivery_date && $delivery_date > $donation['best_before']) {
        die("Invalid delivery date.");
    }

    $new_remaining = $donation['remaining_quantity'] - $quantity;

    $order_stmt = $conn->prepare("
        INSERT INTO orders (
            donation_id,
            charity_name,
            food_item_name,
            quantity,
            best_before,
            charity_address,
            donor_address,
            charity_phone,
            donor_phone,
            status
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");

    $order_stmt->bind_param(
        "ississsss",
        $donation_id,
        $charity_name,
        $donation['food_item_name'],
        $quantity,
        $donation['best_before'],
        $charity_address,
        $donation['pickup_address'],
        $charity_phone,
        $donation['donor_phone']
    );

    $order_stmt->execute();

    $update_stmt = $conn->prepare("
        UPDATE donations 
        SET remaining_quantity = ?,
            status = CASE 
                WHEN ? <= 0 THEN 'assigned'
                ELSE status
            END
        WHERE id = ?
    ");

    $update_stmt->bind_param("iii", $new_remaining, $new_remaining, $donation_id);
    $update_stmt->execute();

    header("Location: order_success.php");
    exit();

} catch (mysqli_sql_exception $e) {
    die("Database Error: " . $e->getMessage());
}
?>