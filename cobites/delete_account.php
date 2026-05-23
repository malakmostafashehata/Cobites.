<?php
require "db.php";
require "auth.php";

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

$id = $_SESSION['user_id'];

$stmt = $conn->prepare("DELETE FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

setcookie("remember_token", "", time() - 3600, "/");

session_unset();
session_destroy();

header("Location: index.php");
exit();
?>