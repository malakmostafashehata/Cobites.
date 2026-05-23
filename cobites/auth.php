<?php
require "db.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["user_id"]) && !empty($_COOKIE["remember_token"])) {

    $tokenHash = hash("sha256", $_COOKIE["remember_token"]);

    $stmt = $conn->prepare("
        SELECT id, full_name, role 
        FROM users 
        WHERE remember_token=? 
        AND token_expires > NOW()
        LIMIT 1
    ");

    $stmt->bind_param("s", $tokenHash);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        session_regenerate_id(true);
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["full_name"] = $user["full_name"];
        $_SESSION["role"] = $user["role"];
    }
}
?>