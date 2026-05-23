<?php
require "db.php";
require "auth.php";
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = strtolower(trim($_POST["email"] ?? ''));
    $password = $_POST["password"] ?? '';

    if (empty($email) || empty($password)) {
        header("Location: index.php?error=empty");
        exit;
    }

    $stmt = $conn->prepare("
        SELECT id, full_name, password, role, status 
        FROM users 
        WHERE email=? 
        LIMIT 1
    ");

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {

        if (!password_verify($password, $user["password"])) {
            header("Location: index.php?error=wrong_password");
            exit;
        }

        if ($user["role"] === "charity" && $user["status"] !== "accepted") {
            header("Location: index.php?error=pending");
            exit;
        }

        session_regenerate_id(true);

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["full_name"] = $user["full_name"];
        $_SESSION["role"] = $user["role"];

        if (!empty($_POST["remember"])) {

            $token = bin2hex(random_bytes(32));
            $tokenHash = hash("sha256", $token);
            $expires = date("Y-m-d H:i:s", time() + (86400 * 30));

            setcookie("remember_token", $token, [
                "expires" => time() + (86400 * 30),
                "path" => "/",
                "httponly" => true,
                "secure" => false,
                "samesite" => "Strict"
            ]);

            $stmt2 = $conn->prepare("
                UPDATE users 
                SET remember_token=?, token_expires=? 
                WHERE id=?
            ");

            $stmt2->bind_param("ssi", $tokenHash, $expires, $user["id"]);
            $stmt2->execute();
        }

        header("Location: homepage.php");
        exit;

    } else {
        header("Location: index.php?error=user_not_found");
        exit;
    }
}
?>