<?php
require "auth.php";

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}
include "db.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        die("Error: Session expired. Please log in again.");
    }

    try {
        $user_query = "SELECT full_name, phone FROM users WHERE id = ?";
        $u_stmt = $conn->prepare($user_query);
        $u_stmt->bind_param("i", $user_id);
        $u_stmt->execute();
        $u_result = $u_stmt->get_result();
        $user_data = $u_result->fetch_assoc();
        
        $donor_name  = !empty($user_data['full_name']) ? $user_data['full_name'] : 'Unknown Donor';
        $donor_phone = !empty($user_data['phone']) ? $user_data['phone'] : 'N/A';

        $_SESSION['full_name'] = $donor_name;

        $category    = $_POST['category'] ?? '';
        $food_name   = trim($_POST['food_item_name'] ?? '');
        $quantity    = $_POST['quantity'] ?? '';
        $best_before = $_POST['best_before'] ?? '';
        $address     = trim($_POST['pickup_address'] ?? '');
        $condition   = $_POST['food_condition'] ?? '';

        if (empty($category) || empty($food_name) || empty($quantity) || empty($best_before) || empty($address)) {
            $_SESSION['error'] = "Please fill out all fields! Make sure to select a Category.";
            $_SESSION['form_data'] = $_POST;
            header("Location: volunteer.php");
            exit();
        }

        $query = "INSERT INTO donations (
                    food_item_name, 
                    quantity, 
                    remaining_quantity, 
                    best_before, 
                    food_condition, 
                    donor_name, 
                    pickup_address, 
                    donor_phone, 
                    category
                  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        
      
        $stmt->bind_param("sssssssss", 
            $food_name, 
            $quantity, 
            $quantity, 
            $best_before, 
            $condition, 
            $donor_name, 
            $address, 
            $donor_phone, 
            $category
        );

        if ($stmt->execute()) {
            header("Location: success.php");
            exit();
        }

    } catch (mysqli_sql_exception $e) {
        die("Database Error: " . $e->getMessage());
    }
} else {
    header("Location: volunteer.php");
    exit();
}
?>