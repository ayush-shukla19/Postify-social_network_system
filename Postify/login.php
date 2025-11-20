<?php
session_start();
require_once "classes/Database.php";
require_once "classes/User.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $db = new Database();
    $userObj = new User($db->conn);

    // Call login function
    $user = $userObj->login($_POST['email'], $_POST['password']);

    if ($user && is_array($user)) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['profile_pic'] = $user['profile_pic'];
        $_SESSION['dob'] = $user['dob'];

        header("Location: profile.php");
        exit();
    } else {
        $message = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Postify - Login</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="login-signup-body">
    <div class="form-container">
        <h2>Login</h2>
        <?php if (!empty($message)) echo "<p class='msg'>$message</p>"; ?>
        <form action="" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p>Don’t have an account? <a href="signup.php">Signup</a></p>
    </div>
</div>
</body>
</html>