<?php
require 'db.php';
require 'auth.php';
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    function clean($data){
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    $name    = clean($_POST['name'] ?? '');
    $email   = clean($_POST['email'] ?? '');
    $message = clean($_POST['message'] ?? '');


    if(empty($name) || empty($email) || empty($message)){
        echo "All fields are required";
        exit;
    }

    if(strlen($name) < 3){
        echo "Name must be at least 3 characters";
        exit;
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        echo "Invalid email address";
        exit;
    }

    if(strlen($message) < 10){
        echo "Message must be at least 10 characters";
        exit;
    }


    $stmt = $conn->prepare(
        "INSERT INTO contact_messages (name,email,message)
         VALUES (?,?,?)"
    );

    if(!$stmt){
        echo "Database error";
        exit;
    }

    $stmt->bind_param("sss",$name,$email,$message);

    if($stmt->execute()){
        echo "success";
    }else{
        echo "Failed to send message";
    }

    $stmt->close();
}
?>