<?php
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Register new user
    public function register($full_name, $email, $password, $re_password, $dob, $profile_pic) {
        if ($password !== $re_password) {
            return "Passwords do not match!";
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $query = "INSERT INTO users (full_name, email, password, dob, profile_pic) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssss", $full_name, $email, $hashedPassword, $dob, $profile_pic);

        return $stmt->execute() ? "Signup successful! Please login." : "Error: " . $stmt->error;
    }

    // Login user
    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                session_start();
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['full_name'] = $row['full_name'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['profile_pic'] = $row['profile_pic'];
                $_SESSION['dob'] = $row['dob']; // changed from age

                header("Location: profile.php");
                exit();
            } else {
                return "❌ Invalid password!";
            }
        } else {
            return "❌ No account found with this email!";
        }
    }

    // Update profile
    public function updateProfile($id, $fullName, $dob, $profilePic) {
        $stmt = $this->conn->prepare("UPDATE users SET full_name=?, dob=?, profile_pic=? WHERE id=?");
        $stmt->bind_param("sssi", $fullName, $dob, $profilePic, $id);

        return $stmt->execute() ? "✅ Profile updated successfully!" : "❌ Error updating profile: " . $this->conn->error;
    }
}
?>