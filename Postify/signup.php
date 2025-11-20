<?php
require_once "classes/Database.php";
require_once "classes/User.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $db = new Database();
    $user = new User($db->conn);

    $uploadDir = "assets/uploads/profile_pics/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $profilePic = $_FILES['profile_pic']['name'] ?? null;
    $tmpName = $_FILES['profile_pic']['tmp_name'] ?? null;

    if ($profilePic) {
        $ext = strtolower(pathinfo($profilePic, PATHINFO_EXTENSION));
        $allowed = ["jpg","jpeg","png"];
        if (in_array($ext,$allowed)) {
            $newName = uniqid() . "." . $ext;
            move_uploaded_file($tmpName, $uploadDir . $newName);
        } else {
            $newName = "default.png";
        }
    } else {
        $newName = "default.png";
    }

    $message = $user->register(
        $_POST['full_name'],
        $_POST['email'],
        $_POST['password'],
        $_POST['re_password'],
        $_POST['dob'],
        $newName
    );
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Postify - Signup</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="login-signup-body">
    <div class="form-container">
        <h2>Join Postify</h2>
        <div class="profile-pic" id="profilePreview"
             style="background-image: url('default.png'); width:100px;height:100px;border-radius:50%;margin:0 auto 10px;background-size:cover;background-position:center;"></div>

        <?php if (!empty($message)) echo "<p class='msg'>$message</p>"; ?>

        <form action="" method="POST" enctype="multipart/form-data">
        <label class="upload-btn">
            Upload Profile Pic
            <input type="file" name="profile_pic" accept=".jpg,.jpeg,.png" style="display:none" onchange="previewImage(event)">
        </label>
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="date" name="dob" placeholder="Date of Birth" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <div class="form-row">
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="re_password" placeholder="Confirm Password" required>
            </div>
            <button type="submit">Sign Up</button>
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</div>

<script>
function previewImage(event){
    let preview = document.getElementById("profilePreview");
    preview.style.backgroundImage = `url(${URL.createObjectURL(event.target.files[0])})`;
}
</script>
</body>
</html>
