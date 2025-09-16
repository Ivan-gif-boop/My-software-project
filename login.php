<?php
// login.php - combined form + processing
require_once 'conf.php';

$errors = [];
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = "Email and password are required.";
    } else {
        // fetch user
        $stmt = $conn->prepare("SELECT id, name, password, is_verified FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                if ($user['is_verified'] == 1) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['name'] = $user['name'];
                    $success = "Login successful! Welcome, " . htmlspecialchars($user['name']) . ".";
                    // Optionally redirect: redirect_to("dashboard.php");
                } else {
                    $errors[] = "Your account is not verified. Please check your email.";
                }
            } else {
                $errors[] = "Invalid password.";
            }
        } else {
            $errors[] = "No account found with that email.";
        }

        $stmt->close();
    }
}
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login</title>
</head>
<body>
<h2>Login</h2>

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

<form method="post" action="login.php">
    <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required></label><br>
    <label>Password: <input type="password" name="password" required></label><br>
    <button type="submit">Login</button>
</form>
</body>
</html>
