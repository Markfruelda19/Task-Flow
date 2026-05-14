<?php
require_once 'includes/header.php';
require_once 'config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<div class="auth-container">
    <h2>Welcome Back</h2>

    <?php if ($error): ?>
        <p class="msg error"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="email"    name="email"    placeholder="Email"    required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <p>No account yet? <a href="register.php">Register</a></p>
</div>

<?php require_once 'includes/footer.php'; ?>