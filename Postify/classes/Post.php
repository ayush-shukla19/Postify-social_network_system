<?php
class Post {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Add new post
    public function addPost($userId, $description, $postImage) {
        $stmt = $this->conn->prepare("INSERT INTO posts (user_id, description, post_image) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $userId, $description, $postImage);

        if ($stmt->execute()) {
            return "✅ Post added successfully!";
        } else {
            return "❌ Error: " . $this->conn->error;
        }
    }

    // Get all posts by a user
    public function getPostsByUser($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM posts WHERE user_id=? ORDER BY created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // ✅ Delete post (with image removal)
    public function deletePost($postId, $userId) {
        // 1. Get the image name first
        $stmt = $this->conn->prepare("SELECT post_image FROM posts WHERE id=? AND user_id=?");
        $stmt->bind_param("ii", $postId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $post = $result->fetch_assoc();

        if ($post) {
            // 2. If there is an image, delete it from the folder
            if (!empty($post['post_image'])) {
                $filePath = "assets/uploads/posts/" . $post['post_image'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // 3. Delete the post from database
            $stmt = $this->conn->prepare("DELETE FROM posts WHERE id=? AND user_id=?");
            $stmt->bind_param("ii", $postId, $userId);
            return $stmt->execute();
        }

        return false;
    }

    // Like/Dislike
    public function reactToPost($userId, $postId, $reaction) {
        // Check if user already reacted
        $check = $this->conn->prepare("SELECT * FROM post_reactions WHERE user_id=? AND post_id=?");
        $check->bind_param("ii", $userId, $postId);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            // Update reaction
            $stmt = $this->conn->prepare("UPDATE post_reactions SET reaction=? WHERE user_id=? AND post_id=?");
            $stmt->bind_param("sii", $reaction, $userId, $postId);
            $stmt->execute();
        } else {
            // Insert new reaction
            $stmt = $this->conn->prepare("INSERT INTO post_reactions (user_id, post_id, reaction) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $userId, $postId, $reaction);
            $stmt->execute();
        }

        // Update post like/dislike counts
        $likes = $this->countReactions($postId, 'like');
        $dislikes = $this->countReactions($postId, 'dislike');

        $update = $this->conn->prepare("UPDATE posts SET likes=?, dislikes=? WHERE id=?");
        $update->bind_param("iii", $likes, $dislikes, $postId);
        $update->execute();

        return ["likes" => $likes, "dislikes" => $dislikes];
    }

    private function countReactions($postId, $reaction) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM post_reactions WHERE post_id=? AND reaction=?");
        $stmt->bind_param("is", $postId, $reaction);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row['count'];
    }
}
?>
