<?php
require_once 'includes/header.php';
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $error = "Email is already registered.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashed]);
            $success = "Account created! <a href='login.php'>Login here</a>.";
        }
    }
}
?>

<div class="auth-container">
    <h2>Create Account</h2>

    <?php if ($error): ?>
        <p class="msg error"><?= $error ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="msg success"><?= $success ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text"     name="username"         placeholder="Username"         required>
        <input type="email"    name="email"            placeholder="Email"            required>
        <input type="password" name="password"         placeholder="Password"         required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login</a></p>
</div>

<?php require_once 'includes/footer.php'; ?>