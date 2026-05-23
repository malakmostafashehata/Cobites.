<?php
require "db.php";
require "auth.php";
if (isset($_SESSION["user_id"])) {
    $stmt = $conn->prepare("
        UPDATE users
        SET remember_token=NULL, token_expires=NULL
        WHERE id=?
    ");
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
}

session_unset();
session_destroy();

setcookie("remember_token","",time()-3600,"/");

header("Location: homepage.php");
exit; ?>