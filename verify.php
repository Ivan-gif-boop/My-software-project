<?php
// verify.php - handles email verification (Software Project)

include("conf.php"); // database connection

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // find user with this token
    $stmt = $conn->prepare("SELECT id FROM users WHERE verify_token = ? AND is_verified = 0 LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // token is valid → verify user
        $user = $result->fetch_assoc();
        $update = $conn->prepare("UPDATE users SET is_verified = 1, verify_token = NULL WHERE id = ?");
        $update->bind_param("i", $user['id']);

        if ($update->execute()) {
            echo "Your email has been successfully verified. You can now log in.";
        } else {
            echo "Error: Could not update verification status.";
        }

        $update->close();
    } else {
        echo "Invalid or expired token.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "No token provided.";
}
?>
