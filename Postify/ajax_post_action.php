<?php
session_start();
require_once "classes/Database.php";
require_once "classes/Post.php";

if (!isset($_SESSION['user_id'])) {
    echo "error"; 
    exit;
}

$db = new Database();
$post = new Post($db->conn);
$userId = $_SESSION['user_id'];

if (isset($_POST['action'])) {

    $action = $_POST['action'];

    if ($action === "delete") {
        $postId = intval($_POST['post_id']);
        $success = $post->deletePost($postId, $userId);
        echo $success ? "deleted" : "error";
        exit;
    }
    if ($action === "like" || $action === "dislike") {
        $postId = intval($_POST['post_id']);
        $reaction = $action;
        $result = $post->reactToPost($userId, $postId, $reaction);
        echo json_encode($result);
        exit;
    }
}
?>
