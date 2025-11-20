<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once "classes/Database.php";
require_once "classes/User.php";
require_once "classes/Post.php";

$db = new Database();
$user = new User($db->conn);
$post = new Post($db->conn);

$message = "";

// Ensure upload directories
if(!is_dir("assets/uploads/profile_pics")) mkdir("assets/uploads/profile_pics", 0777, true);
if(!is_dir("assets/uploads/posts")) mkdir("assets/uploads/posts", 0777, true);

// Update profile
if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_profile'])){
    $fullName = $_POST['full_name'];
    $dob = $_POST['dob'];

    $profilePic = $_FILES['profile_pic']['name'] ?? null;
    if($profilePic){
        $ext = strtolower(pathinfo($profilePic, PATHINFO_EXTENSION));
        $allowed = ["jpg","jpeg","png"];
        if(in_array($ext,$allowed)){

            // 🔹 Delete old profile pic (if not default)
            $old = $_SESSION['profile_pic'] ?? '';
            if ($old && $old !== 'default.png' && file_exists("assets/uploads/profile_pics/$old")) {
                @unlink("assets/uploads/profile_pics/$old");
            }

            // 🔹 Upload new profile pic
            $newName = uniqid().".".$ext;
            move_uploaded_file($_FILES['profile_pic']['tmp_name'], "assets/uploads/profile_pics/".$newName);
        } else {
            $newName = $_SESSION['profile_pic'];
        }
    } else {
        $newName = $_SESSION['profile_pic'];
    }

    $message = $user->updateProfile($_SESSION['user_id'], $fullName, $dob, $newName);
    $_SESSION['full_name'] = $fullName;
    $_SESSION['dob'] = $dob;
    $_SESSION['profile_pic'] = $newName;
}

// Add post
if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_post'])){
    $description = $_POST['description'];
    $postImage = "";
    if(!empty($_FILES['post_image']['name'])){
        $ext = strtolower(pathinfo($_FILES['post_image']['name'], PATHINFO_EXTENSION));
        $allowed = ["jpg","jpeg","png"];
        if(in_array($ext,$allowed)){
            $newName = uniqid().".".$ext;
            move_uploaded_file($_FILES['post_image']['tmp_name'], "assets/uploads/posts/".$newName);
            $postImage = $newName;
        }
    }
    $message = $post->addPost($_SESSION['user_id'], $description, $postImage);
}

$allPosts = $post->getPostsByUser($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Postify - Profile</title>
<link rel="stylesheet" href="assets/css/style.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="profile-page">
    <!-- Left Column: Profile -->
    <div class="profile-left">
        <img src="assets/uploads/profile_pics/<?php echo $_SESSION['profile_pic']; ?>" alt="Profile Picture">
        <h3 id="abcd"><?php echo $_SESSION['full_name']; ?></h3>
        <?php if(!empty($message)) echo "<p class='msg'>$message</p>"; ?>

        <form method="POST" enctype="multipart/form-data">
            <div id="abc" >
            <label><small>Full Name:</small></label>
            <input type="text" name="full_name" value="<?php echo $_SESSION['full_name']; ?>" required>

            <label><small>Date of Birth:</small></label>
            <input type="date" name="dob" value="<?php echo $_SESSION['dob'] ?? ''; ?>" required>

            <label><small>Email:</small></label>
            <input type="email" value="<?php echo $_SESSION['email']; ?>" disabled>

            <label><small>Profile Picture:</small></label>
            <input type="file" name="profile_pic" accept=".jpg,.jpeg,.png">
            </div>
            <button type="submit" name="update_profile">Update Profile</button>
        </form>

        <p><a href="logout.php">Logout</a></p>
    </div>

    <!-- Right Column: Posts -->
    <div class="profile-right">
        <form method="POST" enctype="multipart/form-data" class="new-post-form">
            <textarea name="description" placeholder="Write your caption here...." required></textarea>
            <input type="file" name="post_image" accept=".jpg,.jpeg,.png">
            <button type="submit" name="add_post">Add Post</button>
        </form>

        <div class="posts">
            <?php foreach($allPosts as $p): ?>
                <div class="post-card" data-id="<?php echo $p['id']; ?>">
                    <p><strong><?php echo $_SESSION['full_name']; ?></strong></p>
                    <p><?php echo $p['description']; ?></p>
                    <?php if($p['post_image']): ?>
                        <img src="assets/uploads/posts/<?php echo $p['post_image']; ?>" alt="Post Image">
                    <?php endif; ?>
                    <div class="post-actions">
                        <button class="like-btn">&#x2665 <span class="like-count"><?php echo $p['likes']; ?></span></button>
                        <button class="dislike-btn">👎 <span class="dislike-count"><?php echo $p['dislikes']; ?></span></button>
                        <button class="delete-post">🗑 Delete</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script src="assets/js/script.js"></script>
</body>
</html>
