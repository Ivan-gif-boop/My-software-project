<?php
// signup.php
require_once 'conf.php'; // safe include, only once

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    // Basic validation
    if ($username === '' || $email === '' || $password === '' || $password2 === '') {
        $errors[] = 'All fields are required.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if ($password !== $password2) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = 'Email is already registered.';
        } else {
            // Insert user
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $token  = bin2hex(random_bytes(16)); // verification token

            $ins = $conn->prepare("INSERT INTO users (name, email, password, verify_token) VALUES (?, ?, ?, ?)");
            $ins->bind_param('ssss', $username, $email, $hashed, $token);

            if ($ins->execute()) {
                $success = 'Registration successful. Please check your email to verify your account.';
                // TODO: Call send_verification_email() here
            } else {
                $errors[] = 'Database error: could not register user.';
            }

            $ins->close();
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sign Up</title>
</head>
<body>
    <h2>Register</h2>

    <?php if (!empty($errors)): ?>
        <ul style="color:red;">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="post" action="signup.php">
        <label>Username: <input name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required></label><br>
        <label>Email: <input name="email" type="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required></label><br>
        <label>Password: <input name="password" type="password" required></label><br>
        <label>Confirm: <input name="password2" type="password" required></label><br>
        <button type="submit">Sign up</button>
    </form>
</body>
</html>

