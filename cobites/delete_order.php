<?php
require "db.php";
require "auth.php";

if (!isset($_SESSION['full_name'])) {
    exit;
}

$id = intval($_POST['id']);

$stmt = $conn->prepare("SELECT donation_id, quantity, status FROM orders WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if ($order && $order['status'] === 'pending') {

    if (!empty($order['donation_id'])) {
        $restore = $conn->prepare("
            UPDATE donations 
            SET remaining_quantity = remaining_quantity + ?
            WHERE id = ?
        ");
        $restore->bind_param("ii", $order['quantity'], $order['donation_id']);
        $restore->execute();
    }

    $del = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $del->bind_param("i", $id);
    $del->execute();

    echo "success";
}
?>