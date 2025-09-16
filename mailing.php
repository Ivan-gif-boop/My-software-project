<?php
// signup.php - user registration with email verification (Software Project)

include("conf.php"); // database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        echo "All fields are required.";
        exit;
    }

    // check if user already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "An account with this email already exists.";
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();

    // hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, is_verified) VALUES (?, ?, ?, 0)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        $stmt->close();

        // now call mailing.php to send verification email
        // we use CURL to "post" data to mailing.php
        $ch = curl_init("http://localhost/My-software-project/mailing.php");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'email' => $email,
            'username' => $username
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        echo "Signup successful! Please check your email to verify your account.<br>";
        echo $response; // show mailing.php response (success/error)
    } else {
        echo "Error: Could not register user.";
    }

    $conn->close();
} else {
    echo "Invalid request.";
}
?>
